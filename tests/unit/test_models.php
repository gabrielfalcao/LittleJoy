<?php
/* require_once("Little/Joy.php"); */

/* class Person extends ModelJoy { */
/*     var $name = array(type => CharField, max_length => 100); */
/*     var $email = array(type => EmailField); */
/*     var $bio = array(type => TextField); */
/*     var $website = array(type => URLField); */
/*     var $gender = array(type => ChoiceField, nullable => true, choices => array("male", "female")); */
/*     var $birthday = array(type => DateTimeField, nullable => true); */
/* } */
/* $person_ddl = " */
/* CREATE TABLE `Person`( */
/*     `ID` integer AUTO_INCREMENT NOT NULL PRIMARY KEY, */
/*     `name` varchar(100) NOT NULL, */
/*     `email` varchar(255) NOT NULL, */
/*     `bio` longtext NOT NULL, */
/*     `website` varchar(255) NOT NULL, */
/*     `gender` varchar(6) NULL, */
/*     `birthday` datetime NULL */
/* );"; */

/* class Permission extends ModelJoy { */
/*     var $name = array(type => CharField); */
/*     var $person = array(type => ForeignKey, related_with => "Person"); */
/* } */
/* $permission_ddl = " */
/* CREATE TABLE `Permission`( */
/*     `ID` integer AUTO_INCREMENT NOT NULL PRIMARY KEY, */
/*     `name` varchar(255) NOT NULL, */
/*     `person` integer NOT NULL */
/* ); */
/* ALTER TABLE `Permission` ADD CONSTRAINT `Permission__related_with__Person__as__person` FOREIGN KEY (`person`) REFERENCES `Person` (`ID`); */
/* "; */

/* class Group extends ModelJoy { */
/*     var $name = array(type => CharField, max_length => 100); */
/*     var $members = array(type => ManyToManyField, related_with => "Person"); */
/* } */
/* $group_ddl = " */
/* CREATE TABLE `Group`( */
/*     `ID` integer AUTO_INCREMENT NOT NULL PRIMARY KEY, */
/*     `name` varchar(100) NOT NULL */
/* ); */
/* CREATE TABLE `PersonGroup_M2M`( */
/*     `ID` integer AUTO_INCREMENT NOT NULL PRIMARY KEY, */
/*     `Person__ID` integer NOT NULL, */
/*     `Group__ID` integer NOT NULL */
/* ); */
/* ALTER TABLE `PersonGroup_M2M` ADD CONSTRAINT `PersonGroup_M2M` FOREIGN KEY (`Person__ID`) REFERENCES `Person` (`ID`); */
/* ALTER TABLE `PersonGroup_M2M` ADD CONSTRAINT `GroupPerson_M2M` FOREIGN KEY (`Group__ID`) REFERENCES `Group` (`ID`); */
/* "; */

/* class Foo extends ModelJoy { */
/*     var $bar = array(type => CharField, max_length => 100); */
/*     var $baz = array(type => TextField); */
/* } */
/* class TestModelJoy extends PHPUnit_Framework_TestCase { */
/*     public function testPersonDDL() { */
/*         global $person_ddl; */
/*         $this->assertEquals(trim($person_ddl), Person::as_table_string()); */
/*     } */
/*     public function testPermissionDDL() { */
/*         global $permission_ddl; */
/*         $this->assertEquals(trim($permission_ddl), Permission::as_table_string()); */
/*     } */
/*     public function testGroupDDL() { */
/*         global $group_ddl; */
/*         $this->assertEquals(trim($group_ddl), Group::as_table_string()); */
/*     } */
/*     public function testPrepareInsert(){ */
/*         $one = Foo::populated_with(array("bar" => "TDD", "baz" => "rockz")); */
/*         $this->assertEquals($one->bar, "TDD"); */
/*         $this->assertEquals($one->baz, "rockz"); */
/*         $this->assertEquals($one->prepare_insert(), "INSERT INTO `Foo` (`bar`, `baz`) VALUES ('TDD', 'rockz');"); */
/*     } */
/*     public function testPrepareUpdate(){ */
/*         $one = Foo::populated_with(array("bar" => "TDD", "baz" => "rockz", "ID" => 987)); */
/*         $this->assertEquals($one->prepare_update(), "UPDATE `Foo` SET `bar` = 'TDD', `baz` = 'rockz' WHERE ID = 987;"); */
/*     } */
/*     public function testComparation(){ */
/*         $one = Foo::populated_with(array("bar" => "TDD", "baz" => "rockz")); */
/*         $two = Foo::populated_with(array("bar" => "TDD", "baz" => "rockz")); */
/*         $this->assertEquals($one, $two); */
/*     } */
/* } */
?>