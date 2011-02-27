<?php
require_once("Little/Joy.php");
require_once('PHPUnit/Extensions/OutputTestCase.php');

import("Contrib/Less");

class TestJoyServeLess extends PHPUnit_Extensions_OutputTestCase
{
    public function testServeLess()
    {
        $this->expectOutputString(
'body {
  color:"hello world";
  color:hello world;
  color:#00112233;
  color:#992c3742;
  strings:this is a color #112233 and here is my string hello world;
  quoted:"#112233" "a list of keywords becomes a string";
  border:"#112233" 5px;
  height:5.1666666666667;
  height:5px;
  width:0.66666666666667;
  width:1;
  width:0;
}');

        $_SERVER['REQUEST_URI'] = '/assets/css/less1.css';
        $_SERVER['DOCUMENT_ROOT'] = FileSystemJoy::absolute_path(".", __FILE__);

        Joy::and_work(array(
            serve_lesscss_from => FileSystemJoy::path_from_docroot("less-files"),
            serve_lesscss_on => "/assets/css/(?P<path>.*)[.]css$",
        ));

    }
}
?>