<?php
/*
 * Copyright 2012-2018 Damien Seguy � Exakat Ltd <contact(at)exakat.io>
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


namespace Exakat\Analyzer\Classes;

use Exakat\Analyzer\Analyzer;

class UsedProtectedMethod extends Analyzer {
    public function analyze() {
        // method used in a static methodcall \a\b::b()
        $this->atomIs('Class')
             ->outIs(array('METHOD', 'MAGICMETHOD'))
             ->atomIs(array('Method', 'Magicmethod'))
             ->_as('method')
             ->is('visibility', 'protected')
             ->outIs('NAME')
             ->codeIsNot(array('__construct', '__destruct'))
             ->savePropertyAs('lccode', 'name')
             ->back('first')
             ->goToAllChildren(self::EXCLUDE_SELF)
             ->outIs(array('METHOD', 'MAGICMETHOD'))
             ->atomInsideNoDefinition('Staticmethodcall')
             ->outIs('METHOD')
             ->tokenIs('T_STRING')
             ->samePropertyAs('lccode', 'name', self::CASE_INSENSITIVE)
             ->back('method');
        $this->prepareQuery();

        // method used in a normal methodcall with $this $this->b()
        $this->atomIs('Class')
             ->outIs(array('METHOD', 'MAGICMETHOD'))
             ->atomIs(array('Method', 'Magicmethod'))
             ->_as('method')
             ->is('visibility', 'protected')
             ->outIs('NAME')
             ->codeIsNot(array('__construct', '__destruct'))
             ->savePropertyAs('lccode', 'name')
             ->back('first')
             ->goToAllChildren(self::EXCLUDE_SELF)
             ->outIs(array('METHOD', 'MAGICMETHOD'))
             ->atomInsideNoDefinition('Methodcall')
             ->outIs('OBJECT')
             ->atomIs('This')
             ->inIs('OBJECT')
             ->outIs('METHOD')
             ->tokenIs('T_STRING')
             ->samePropertyAs('lccode', 'name', self::CASE_INSENSITIVE)
             ->back('method');
        $this->prepareQuery();
    }
}

?>
