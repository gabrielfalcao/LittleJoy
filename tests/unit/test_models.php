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
$person_ddl = "
CREATE TABLE `Person`(
    `ID` integer AUTO_INCREMENT NOT NULL PRIMARY KEY,
    `name` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `bio` longtext NOT NULL,
    `website` varchar(255) NOT NULL,
    `gender` varchar(6) NULL,
    `birthday` datetime NULL
);";

class Permission extends ModelJoy {
    var $person = array(type => ForeignKey, related_with => "Person");
    var $name = array(type => CharField, max_length => 100);
    var $group = array(type => ForeignKey, related_with => "Group");
}
class Group extends ModelJoy {
    var $name = array(type => CharField, max_length => 100);
    var $members = array(type => ManyToManyField, related_with => "Person");
}
class TestModelJoy extends PHPUnit_Framework_TestCase {
    public function testPersonDDL() {
        global $person_ddl;
        $this->assertEquals(trim($person_ddl), Person::as_table_string());
    }
    public function testPermissionDDL() {
        global $permission_ddl;
        $this->assertEquals(trim($permission_ddl), Permission::as_table_string());
    }

}

?>