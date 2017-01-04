<?php
/*
 * Copyright 2012-2017 Damien Seguy – Exakat Ltd <contact(at)exakat.io>
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


namespace Exakat\Exceptions;

use Exakat\Analyzer\Analyzer;

class NoSuchAnalyzer extends \RuntimeException {
    public function __construct($analyzer) {
        $die = "Couldn't find '$analyzer'. Aborting\n";
        
        if (preg_match('#[a-z0-9_]+/[a-z0-9_]+$#i', $analyzer)) {
            $r = Analyzer::getSuggestionClass($analyzer);
            if (count($r) > 0) {
                $die .= 'Did you mean : '.implode(', ', array_slice($r, 0, 5));
                if (count($r) > 5) {
                    $die .= " (More available)";
                }
                $die .= "\n";
            } else {
                $die .= "Couldn't find a suggestion. Check the documentation http://exakat.readthedocs.io/\n";
            }
        } else {
            $die .= "Analyzers use the format Folder/Rule, for example Structures/UselessInstructions. Check the documentation http://exakat.readthedocs.io/\n";
        }

        parent::__construct($die);
    }
}

?>