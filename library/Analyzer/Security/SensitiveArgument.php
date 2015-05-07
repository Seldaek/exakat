<?php
/*
 * Copyright 2012-2015 Damien Seguy – Exakat Ltd <contact(at)exakat.io>
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


namespace Analyzer\Security;

use Analyzer;

class SensitiveArgument extends Analyzer\Analyzer {
    public function analyze() {
        $unsafe = $this->loadIni('security_vulnerable_functions.ini');
        
        $positions = array(0, 1, 2);
        
        foreach($positions as $position) {
            $functions = $this->makeFullNsPath($unsafe['functions'.$position]);

            // $_GET/_POST ... directly as argument of PHP functions
            $this->atomIs('Functioncall')
                 ->hasNoIn('METHOD')
                 ->tokenIs(array('T_STRING', 'T_NS_SEPARATOR', 'T_DIE', 'T_EXIT', 'T_ECHO', 'T_PRINT'))
                 ->fullnspath($functions)
                 ->outIs('ARGUMENTS')
                 ->outIs('ARGUMENT')
                 ->is('rank', $position);
            $this->prepareQuery();
        }
    }
}

?>
