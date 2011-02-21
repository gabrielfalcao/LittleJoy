<?php
class TestControllerJoyExceptions extends PHPUnit_Framework_TestCase {
    public function testIncompleteControllerException1() {
        $this->setExpectedException(IncompleteControllerError);
        import("FaultyController1", __FILE__);
        FaultyController1::get_route_for("anywhere");
    }
    public function testIncompleteControllerException2() {
        $this->setExpectedException(WrongURLSet);
        import("FaultyController2", __FILE__);
        FaultyController2::get_route_for("anywhere");
    }
}
?>
