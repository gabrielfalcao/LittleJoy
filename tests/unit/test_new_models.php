<?php
require_once("Little/Joy.php");

class UserModelBusinessRules {
    public function get_full_name(){
        return $this->first_name . " " . $this->last_name;
    }
}
Entity("User", function($does, $validate, $table){
        $does->have("username", new CharField(30));
        $does->have("first_name", new CharField(30));
        $does->have("last_name", new CharField(30));
        $does->have("email", new EmailField());
        $does->have("password", new CharField(128));
        $does->have("is_staff", new BooleanField());
        $does->have("is_active", new BooleanField());
        $does->have("is_superuser", new BooleanField());
        $does->have("last_login", new DateTimeField());
        $does->have("date_joined", new DateTimeField());

        $does->has_many("groups")->from_entity("Group")->through("auth_user_groups");

        $does->validate_uniqueness_of("username");

        $table->name_is("auth_user");
        $does->extend("UserModelBusinessRules");
    });

Entity("Group", function($does, $validate, $table){
        $does->have("name", new CharField(80));
        $does->has_many("users")->from_entity("User")->through("auth_user_groups");
        $table->name_is("auth_group");
    });

$USER_DDL = "CREATE TABLE `auth_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `email` varchar(75) NOT NULL,
  `password` varchar(128) NOT NULL,
  `is_staff` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_superuser` tinyint(1) NOT NULL,
  `last_login` datetime NOT NULL,
  `date_joined` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

class TestEntity extends PHPUnit_Framework_TestCase {
    public function testPersonDDL() {global $USER_DDL;
        $this->assertEquals(trim($USER_DDL), User::sql_for(create));
    }
}

?>