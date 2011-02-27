<?php

class TestFileSystemJoy extends PHPUnit_Framework_TestCase {
    public function setUp(){
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/';
    }
    public function testAbsolutePathDefaultsToDocroot() {
        $this->assertEquals($_SERVER['DOCUMENT_ROOT'], '/var/www/');
        $this->assertEquals(FileSystemJoy::absolute_path('.'), "/var/www/");
    }
    public function testAbsolutePathOtherBasepath() {
        $this->assertEquals($_SERVER['DOCUMENT_ROOT'], '/var/www/');
        $this->assertEquals(FileSystemJoy::absolute_path('joy-on', '/foo/bar'), "/foo/bar/joy-on");
    }
    public function testAbsolutePathDefaultsToDocrootEvenWithArray() {
        $this->assertEquals($_SERVER['DOCUMENT_ROOT'], '/var/www/');
        $this->assertEquals(FileSystemJoy::absolute_path(array("path", "properly", "joined")), "/var/www/path/properly/joined");
    }
    public function testAbsolutePathOtherBasepathEvenWithArray() {
        $this->assertEquals($_SERVER['DOCUMENT_ROOT'], '/var/www/');
        $this->assertEquals(FileSystemJoy::absolute_path(array('joy', 'on'), '/foo/bar'), "/foo/bar/joy/on");
    }
}
?>