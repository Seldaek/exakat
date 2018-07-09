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


namespace Exakat\Analyzer\Functions;

use Exakat\Analyzer\Analyzer;

class LoopCalling extends Analyzer {
    public function analyze() {
        // loop of 2
        $this->atomIs('Function')
             ->savePropertyAs('fullnspath', 'name')
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')

             ->raw('has("fullnspath")')
             ->notSamePropertyAs('fullnspath', 'name')

             ->functionDefinition()
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')
             ->raw('has("fullnspath")')

             ->samePropertyAs('fullnspath', 'name')
             ->back('first')
             ->outIs('NAME');
        $this->prepareQuery();

        // loop of 3
        $this->atomIs('Function')
             ->savePropertyAs('fullnspath', 'name')
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')
             ->raw('has("fullnspath")')
             ->notSamePropertyAs('fullnspath', 'name')

             ->functionDefinition()
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')
             ->raw('has("fullnspath")')
             ->notSamePropertyAs('fullnspath', 'name')

             ->functionDefinition()
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')
             ->tokenIs(array('T_STRING', 'T_NS_SEPARATOR'))
             ->hasNoIn('METHOD')
             ->samePropertyAs('fullnspath', 'name')

             ->back('first')
             ->outIs('NAME');
        $this->prepareQuery();

        // loop of 4
        $this->atomIs('Function')
             ->savePropertyAs('fullnspath', 'name')
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')
             ->raw('has("fullnspath")')
             ->notSamePropertyAs('fullnspath', 'name')

             ->functionDefinition()
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')
             ->raw('has("fullnspath")')
             ->notSamePropertyAs('fullnspath', 'name')

             ->functionDefinition()
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')
             ->raw('has("fullnspath")')
             ->notSamePropertyAs('fullnspath', 'name')

             ->functionDefinition()
             ->outIs('BLOCK')
             ->atomInsideNoDefinition('Functioncall')
             ->raw('has("fullnspath")')
             ->samePropertyAs('fullnspath', 'name')

             ->back('first')
             ->outIs('NAME');
        $this->prepareQuery();
    }
}

?>
