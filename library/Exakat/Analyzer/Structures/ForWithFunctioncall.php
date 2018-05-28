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


namespace Exakat\Analyzer\Structures;

use Exakat\Analyzer\Analyzer;

class ForWithFunctioncall extends Analyzer {
    public function analyze() {
        $MAX_LOOPING = self::MAX_LOOPING;

        $this->atomIs('For')
            // This looks for variables inside the INCREMENT
             ->raw(<<<GREMLIN
where( 
    __.sideEffect{variables = [];}
      .out("INCREMENT").repeat( __.out({$this->linksDown})).emit().times($MAX_LOOPING)
      .hasLabel("Variable")
      .sideEffect{ variables.push(it.get().value("code")); }
      .fold()
)
GREMLIN
)
             ->outIs('FINAL')
             ->atomInsideNoDefinition('Functioncall')
            // This checks for usage of increment variables inside the FINAL
             ->raw(<<<GREMLIN
not(
    __.where( 
        __.repeat( __.out({$this->linksDown})).emit().times($MAX_LOOPING)
          .hasLabel("Variable")
          .filter{ it.get().value("code") in variables; }
    )
)
GREMLIN
)
             ->back('first');
        $this->prepareQuery();

        $this->atomIs('For')
             ->outIs('INCREMENT')
             ->atomInsideNoDefinition('Functioncall')
             ->back('first');
        $this->prepareQuery();
    }
}

?>
