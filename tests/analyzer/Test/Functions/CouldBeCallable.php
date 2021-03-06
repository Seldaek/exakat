<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Functions_CouldBeCallable extends Analyzer {
    /* 4 methods */

    public function testFunctions_CouldBeCallable01()  { $this->generic_test('Functions/CouldBeCallable.01'); }
    public function testFunctions_CouldBeCallable02()  { $this->generic_test('Functions/CouldBeCallable.02'); }
    public function testFunctions_CouldBeCallable03()  { $this->generic_test('Functions/CouldBeCallable.03'); }
    public function testFunctions_CouldBeCallable04()  { $this->generic_test('Functions/CouldBeCallable.04'); }
}
?>