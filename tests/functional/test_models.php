<?php
require_once("Little/Joy.php");

class Person extends ModelJoy {
    var $name = array(type => CharField, max_length => 100);
    var $email = array(type => EmailField);
}
class Group extends ModelJoy {
    var $name = array(type => CharField, max_length => 100);
    var $members = array(type => ManyToManyField, related_with => "Person");
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
        mysql_query("INSERT INTO `Person` (`name`,`email`) VALUES ('Gabriel', 'gabriel@nacaolivre.org');", $this->conn);
        Person::syncdb(false);
        $res = mysql_query("SELECT * FROM Person;", $this->conn);
        $object = mysql_fetch_object($res);
        $this->assertEquals($object->name, "Gabriel");
        $this->assertEquals($object->email, "gabriel@nacaolivre.org");
    }

    public function testPersonCreateTableForcesReset() {
        $this->conn = Joy::connect_to_mysql_database("localhost", "root", null, "my_database");
        Person::syncdb();
        mysql_query("INSERT INTO `Person` (`name`,`email`) VALUES ('Gabriel', 'gabriel@nacaolivre.org');", $this->conn);
        Person::syncdb(true);
        $res = mysql_query("SELECT * FROM Person;", $this->conn);
        $object = mysql_fetch_object($res);
        $this->assertEquals($object, null);
    }

    public function testPersonSave() {
        Joy::set_mysql_database("localhost", "root", null, "my_database");
        Joy::syncdb();

        $gabriel = Person::populated_with(array("name" => "Gabriel", "email" => "gabriel@nacaolivre.org"));
        $this->assertEquals($gabriel->name, "Gabriel");
        $this->assertEquals($gabriel->email, "gabriel@nacaolivre.org");

        $gabriel->save();

        $this->conn = Joy::connect_to_registered_database();
        $res = mysql_query("SELECT * FROM Person;", $this->conn);
        $object = mysql_fetch_object($res);
        $this->assertEquals($object->ID, 1);
        $this->assertEquals($object->name, "Gabriel");
        $this->assertEquals($object->email, "gabriel@nacaolivre.org");
    }
    public function testPersonSaveUpdating() {
        Joy::set_mysql_database("localhost", "root", null, "my_database");
        Joy::syncdb();

        $gabriel = Person::populated_with(array("name" => "Gabriel", "email" => "gabriel@nacaolivre.org"));
        $gabriel->save();
        $gabriel->name = "Gabriel Falcão";
        $gabriel->save();

        $this->conn = Joy::connect_to_registered_database();
        $res = mysql_query("SELECT COUNT(ID) as total FROM Person;", $this->conn);
        $object = mysql_fetch_object($res);
        $this->assertEquals($object->total, "1");

        $res = mysql_query("SELECT * FROM Person;", $this->conn);
        $object = mysql_fetch_object($res);
        $this->assertEquals($object->ID, 1);
        $this->assertEquals($object->name, "Gabriel Falcão");
        $this->assertEquals($object->email, "gabriel@nacaolivre.org");
    }

    public function testPersonFindOne() {
        Joy::set_mysql_database("localhost", "root", null, "my_database");
        Joy::syncdb();

        $gabriel = Person::populated_with(array("name" => "Gabriel", "email" => "gabriel@nacaolivre.org"));
        $gabriel->save();

        $found1 = Person::find_one_by_name("Gabriel");
        $this->assertEquals($found1->name, "Gabriel");
        $this->assertEquals($found1->email, "gabriel@nacaolivre.org");

        $found2 = Person::find_one_by_email("gabriel@nacaolivre.org");
        $this->assertEquals($found2->name, "Gabriel");
        $this->assertEquals($found2->email, "gabriel@nacaolivre.org");
        $this->assertEquals($found2->email, "gabriel@nacaolivre.org");
    }

}

?>