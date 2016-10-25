<?php
/*
 * Copyright 2012-2016 Damien Seguy – Exakat Ltd <contact(at)exakat.io>
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


namespace Exakat\Tasks;

use Exakat\Config;
use Exakat\Analyzer\Analyzer;
use Exakat\Exceptions\ProjectNeeded;
use Exakat\Reports\Reports as Report;

class Report2 extends Tasks {
    public function run(Config $config) {
        if ($config->project == "default") {
            throw new ProjectNeeded();
        }
        
        $reportClass = '\\Exakat\\Reports\\'.$config->format;

        if (!class_exists($reportClass)) {
            die("Format '".$config->format."' doesn't exist. Choose among : ".implode(', ', Report::FORMATS)."\nAborting\n");
        }

        if (!file_exists($config->projects_root.'/projects/'.$config->project)) {
            die("Project '{$config->project} doesn't exist yet. Run init to create it.\nAborting\n");
        }

        if (!file_exists($config->projects_root.'/projects/'.$config->project.'/datastore.sqlite')) {
            die("Project hasn't been analyzed. Run project first.\nAborting\n");
        }

        Analyzer::$datastore = $this->datastore;
        // errors, warnings, fixable and filename
        // line number => columnnumber => type, source, severity, fixable, message

        $dumpFile = $config->projects_root.'/projects/'.$config->project.'/dump.sqlite';

        $max = 20;
        while (!file_exists($dumpFile)) {
            display("$config->project/dump.sqlite doesn't exist yet ($max). Waiting\n");
            sleep(rand(1,3));
            --$max;
            
            if ($max == 0) {
                die("Waited for dump.sqlite, but it never came. Try again later\n");
            }
        }

        $ProjectDumpSql = 'SELECT count FROM resultsCounts WHERE analyzer LIKE "Project/Dump"';
        $dump = new \Sqlite3($dumpFile, \SQLITE3_OPEN_READONLY);
        $res = $dump->query($ProjectDumpSql);
        $row = $res->fetchArray(\SQLITE3_NUM);

        $max = 20;
        while( $row[0] != 1) {
            unset($dump);
            sleep(rand(1,3));
            display("No Dump finish signal ($max). Waiting\n");

            $dump = new \Sqlite3($dumpFile, \SQLITE3_OPEN_READONLY);

            $res = @$dump->query($ProjectDumpSql);
            if ($res) {
                $row = $res->fetchArray(\SQLITE3_NUM);
            }
            
            --$max;
            if ($max == 0) {
                display("Waited for Project/Dump, but it never came. Try again later\n");
                die();
            }
        }

        display( 'Building report for project '.$config->project.' in file "'.$config->file.'", with format '.$config->format."\n");
        $begin = microtime(true);
        
        // Choose format from options

        $report = new $reportClass();
        if ($config->file == 'stdout') {
            echo $report->generate( $config->projects_root.'/projects/'.$config->project);
        } else {
            $report->generate( $config->projects_root.'/projects/'.$config->project, $config->file);
        }
        display("Reported ".$report->getCount()." messages in $config->format\n");
        $end = microtime(true);

        display( "Processing time : ".number_format($end - $begin, 2)." s\n");
        $this->datastore->addRow('hash', array($config->format => $config->file));
        display( "Done\n");
    }
}

?>
