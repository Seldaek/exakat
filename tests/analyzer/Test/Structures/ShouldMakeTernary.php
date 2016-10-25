<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Structures_ShouldMakeTernary extends Analyzer {
    /* 2 methods */

    public function testStructures_ShouldMakeTernary01()  { $this->generic_test('Structures/ShouldMakeTernary.01'); }
    public function testStructures_ShouldMakeTernary02()  { $this->generic_test('Structures/ShouldMakeTernary.02'); }
}
?>