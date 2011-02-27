<?php
require_once("Little/Joy.php");
require_once('PHPUnit/Extensions/OutputTestCase.php');

class HamlController extends ControllerJoy {
    var $urls = array(
        "^mood/([^/]+)" => 'render_haml',
    );
    public function render_haml ($response, $params) {
        $now = "22/02/2011";
        $mood = $params[1];
        return render_view('view1.haml',array("today"=>$now,"mood"=>$mood),__FILE__);
    }
}

class TestControllerRenderHaml extends PHPUnit_Extensions_OutputTestCase
{
    public function testExpectHaml1()
    {

        $this->expectOutputString(
"<div id=\"profile\">
  <div class=\"description\">
    <span id=\"mood\">22/02/2011 is a happy day !</span>
  </div>
</div>");
        $_SERVER['QUERY_STRING'] = 'nocache=true';
        $_SERVER['REQUEST_URI'] = '/mood/happy/?nocache=true';
        Joy::and_work();
    }
}
?>