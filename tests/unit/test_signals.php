<?php
require_once("Little/Joy.php");
require_once('PHPUnit/Extensions/OutputTestCase.php');

class MyModel {
    var $name = "FooBar";
}

class TestSignalJoy extends PHPUnit_Extensions_OutputTestCase {
    public function testFindListeners() {
        BeforeJoy("creating-the-table", function($things){
                echo "creating {$things->model->name} in database... ";
            });

        AfterJoy("creating-the-table", function($things){
                if ($things->got_fine) {
                    echo "OK";
                } else {
                    echo "Failed";
                }
            });
        $m = new MyModel();
        SignalJoy::yell_before_joy("creating-the-table", true, array('model' => $m));
        SignalJoy::yell_after_joy("creating-the-table", false);

        $this->expectOutputString('creating FooBar in database... Failed');
    }
}

?>