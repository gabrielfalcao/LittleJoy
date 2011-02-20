<?php
require_once("Little/Joy.php");

class Person extends ModelJoy {
    var $name = array(type => CharField, max_length => 100);
    var $email = array(type => EmailField);
    var $bio = array(type => TextField);
    var $website = array(type => URLField);
    var $gender = array(type => ChoiceField, nullable => true, choices => array("male", "female"));
    var $birthday = array(type => DateTimeField, nullable => true);
}

class TestModelJoy extends PHPUnit_Framework_TestCase {
    public function setUp(){
        $this->conn = mysql_connect("localhost", "root", "");
        mysql_query("CREATE DATABASE my_database", $this->conn);
        mysql_close($this->conn);
    }
    public function tearDown(){
        $this->conn = mysql_connect("localhost", "root", "");
        mysql_query("DROP DATABASE my_database", $this->conn);
        mysql_close($this->conn);
    }

    public function testPersonCreateTable() {
        $this->conn = Joy::connect_to_mysql_database("localhost", "root", null, "my_database");
        Person::syncdb();
        mysql_query("INSERT INTO `Person` (`ID`,`name`,`email`,`bio`,`website`,`gender`,`birthday`) VALUES( 1, 'Gabriel', 'gabriel@nacaolivre.org', '', 'http://gabrielfalcao.com', 'male', '1988-02-25 00:00:00');", $this->conn);
        $res = mysql_query("SELECT * FROM Person;", $this->conn);
        $object = mysql_fetch_object($res);
        $this->assertEquals($object->name, "Gabriel");
        $this->assertEquals($object->email, "gabriel@nacaolivre.org");
    }

}

?>