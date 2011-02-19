<?php
class TestJoy extends PHPUnit_Framework_TestCase {
    public function testHasVersion() {
        $this->assertEquals(Joy::version(), "0.1",
            "The version of LittleJoy is updated"
        );
    }
}
?>