<?php
require_once("Little/Joy.php");

class User1 extends ModelJoy {
    var $name = array(type => CharField, max_length => 100);
    var $password = array(type => CharField, max_length => 100);
    var $email = array(type => EmailField);
}

class TestJoy extends PHPUnit_Framework_TestCase {
    public function setUp(){
        $this->conn = mysql_connect("localhost", "root", "");
        mysql_query("CREATE DATABASE my_database", $this->conn);
        mysql_close($this->conn);
    }
    public function _tearDown(){
        $this->conn = mysql_connect("localhost", "root", "");
        mysql_query("DROP DATABASE my_database", $this->conn);
        mysql_close($this->conn);
    }

    public function testDatabaseNotFound() {
        $this->conn = mysql_connect("localhost", "root", "");
        mysql_query("DROP DATABASE my_database", $this->conn);
        mysql_close($this->conn);

        $this->setExpectedException(DatabaseDoesNotExist);
        Joy::connect_to_mysql_database("localhost", "root", null, "my_database");

    }
    public function testSyncDB() {
        Joy::set_mysql_database("localhost", "root", null, "my_database");
        Joy::syncdb();

        $this->conn = Joy::connect_to_registered_database();
        mysql_query("INSERT INTO `User1` (`ID`,`name`,`email`,`password`) VALUES( 1, 'Gabriel', 'gabriel@nacaolivre.org', '123456');", $this->conn);
        $res = mysql_query("SELECT * FROM User1;", $this->conn);
        $object = mysql_fetch_object($res);
        $this->assertEquals($object->name, "Gabriel");
        $this->assertEquals($object->email, "gabriel@nacaolivre.org");
        $this->assertEquals($object->password, "123456");
    }

}

?>