<?php
require_once(APPPATH.'/libraries/simpletest/autorun.php');

class CanAddUp extends UnitTestCase {
    function testOneAndOneMakesTwo() {
        $this->assertEqual(1 + 1, 2);
    }
}