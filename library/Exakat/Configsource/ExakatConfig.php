<?php
/*
 * Copyright 2012-2018 Damien Seguy – Exakat Ltd <contact(at)exakat.io>
 * This file is part of Exakat.
 *
 * Exakat is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Exakat is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Exakat.  If not, see <http://www.gnu.org/licenses/>.
 *
 * The latest code can be found at <http://exakat.io/>.
 *
*/

namespace Exakat\Configsource;

use Exakat\Phpexec;
use Exakat\Config as MainConfig;

class ExakatConfig extends Config {
    private $projects_root = '';

    private $gremlins = array( 'tinkergraph' => 'Tinkergraph',
                               'gsneo4j'     => 'GSNeo4j',
                               'bitsy'       => 'Bitsy',
                               'janusgraph'  => 'Janusgraph',
                               'nogremlin'   => 'NoGremlin',
                               );

    private $loaders = array( 'tinkergraph' => 'SplitGraphson',
                              'gsneo4j'     => 'SplitGraphson',
                              'bitsy'       => 'SplitGraphson',
                              'janusgraph'  => 'Janusgraph',
                              'nogremlin'   => 'NoLoader',
                              );

    public function __construct($projects_root) {
        $this->projects_root = $projects_root;
    }

    public function loadConfig($args) {
        // Default values
        $inis[] =   array('graphdb'            => 'gsneo4j',
                          'gremlin'            => $this->gremlins['gsneo4j'],
                          'loader'             => $this->loaders['gsneo4j'],
                          'other_php_versions' => array(),
                          'transit_key'        => '',
                       );

        $configFiles = array("{$this->projects_root}/config/exakat.ini",
                             '/etc/exakat/exakat.ini',
                             '/etc/exakat.ini',
                             );

        // Attempt each init path, and stop at the first file we find
        $ini = null;
        foreach($configFiles as $id => $configFile) {
            if (file_exists($configFile)) {
                // overwrite existing with the new, keep the default values
                $inis = parse_ini_file($configFile) + $inis[0]; 
                $optionFiles = $configFile;
            } 
        }

        if ($inis === null) {
            return self::NOT_LOADED;
        }
        
        $this->config = $inis;

        // Validation
        if (!isset($this->config['graphdb']) || 
            !in_array($this->config['graphdb'], array_keys($this->gremlins)) ) {
            $this->config['graphdb'] = 'gsneo4j';
        }

        $graphdb = $this->config['graphdb'];
        foreach(array_keys($this->gremlins) as $gdb) {
            $folder = "{$gdb}_folder";
            if (isset($this->config[$folder])) {
                if ($this->config[$folder][0] !== '/') {
                    $this->config[$folder] = "{$this->projects_root}/{$this->config[$folder]}";
                }
                $this->config[$folder] = realpath($this->config[$folder]);
            }
        }

        // Update values with actual loaders and gremlin
        $this->config['gremlin'] = $this->gremlins[$this->config['graphdb']];
        $this->config['loader']  = $this->loaders[$this->config['graphdb']];
        
        if (isset($this->config['concurencyCheck'])) {
            $this->config['concurencyCheck'] = (int) $this->config['concurencyCheck'];
            if ($this->config['concurencyCheck'] < 1024) {
                $this->config['concurencyCheck'] = 7610;
            } elseif ($this->config['concurencyCheck'] > 49150) {
                $this->config['concurencyCheck'] = 7610;
            }
        }

        foreach(MainConfig::PHP_VERSIONS as $version) {
            if (empty($this->config["php$version"])) {
                continue;
            }
            $php = new Phpexec("$version[0].$version[1]", $this->config["php$version"]);
            if ($php->isValid()) {
                $this->config['other_php_versions'][] = $version;
            }
        }

        return 'config/exakat.ini';
    }
}

?>