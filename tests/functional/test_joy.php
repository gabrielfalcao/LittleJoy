<?php
require_once("Little/Joy.php");

Entity("User1", function($does, $validate, $table){
        $does->have("name", new CharField(30));
        $does->have("password", new CharField(128));
        $does->have("email", new EmailField());

        $table->name_is("tests_functional_test_joy_user");
    });

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
        mysql_query("INSERT INTO `tests_functional_test_joy_user` (`ID`,`name`,`email`,`password`) VALUES( 1, 'Gabriel', 'gabriel@nacaolivre.org', '123456');", $this->conn);
        $res = mysql_query("SELECT * FROM tests_functional_test_joy_user;", $this->conn);
        $object = mysql_fetch_object($res);
        $this->assertEquals($object->name, "Gabriel");
        $this->assertEquals($object->email, "gabriel@nacaolivre.org");
        $this->assertEquals($object->password, "123456");
    }

}

?>