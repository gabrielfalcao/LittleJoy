<?php
/***********************************************************************
 <LittleJoy - Really tiny framework for php, aimed on testing>
 Copyright (C) <2011>  Gabriel Falcão <gabriel@nacaolivre.org>

 Permission is hereby granted, free of charge, to any person
 obtaining a copy of this software and associated documentation
 files (the "Software"), to deal in the Software without
 restriction, including without limitation the rights to use,
 copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the
 Software is furnished to do so, subject to the following
 conditions:

 The above copyright notice and this permission notice shall be
 included in all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 OTHER DEALINGS IN THE SOFTWARE.

***********************************************************************/
$GLOBALS["__little_joy_entities__"] = array();
$GLOBALS["__little_joy_declared_fields__"] = array();

define("create", "create");
define("drop", "drop");
define("insert", "insert");
define("delete", "delete");
define("update", "update");

class SQLManager {
    public function SQLManager ($entity){
        $this->entity = $entity;
    }
    public function create (){
        $sql_list = array(
            "CREATE TABLE `{$this->entity->table->name}` (",
            "  `id` int(11) NOT NULL AUTO_INCREMENT,",
        );
        $unique_fields = array();
        foreach ($this->entity->get_fields() as $fieldname => $field) {
            if (is_subclass_of($field, "Field")) {
                $sql_list []= $field->as_sql();
                if ($field->unique) {
                    $unique_fields []= $fieldname;
                }
            } else {
                throw new Exception("{$this->entity}->{$fieldname} is supposed to be a field");
            }
        }

        $sql_list []= "  PRIMARY KEY (`id`),";
        foreach ($unique_fields as $fieldname) {
            $sql_list []= "  UNIQUE KEY `{$fieldname}` (`{$fieldname}`)";
        }

        $sql_list []= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        return implode("\n", $sql_list);
    }
}

class Relationship {
    public function Relationship($with_entity, $through=null){
        $this->entity = $with_entity;
        $this->through = $through;
    }
}

class OneToOne extends Relationship {}
class OneToMany extends OneToOne {}
class ManyToOne extends Relationship {
    public function ManyToOne ($fieldname) {
        $this->fieldname = $fieldname;
    }
    public function from_entity($entity){
        $this->from_entity = $entity;
        $this->m2m_table_name = $this->get_related_field_name().$this->fieldname;
        return $this;
    }
    public function get_related_field_name(){
        return "SHOULD_BE_RELATED_FIELD_NAME";
    }
    public function through($entity){
        $this->from_entity = $entity;
        return $this;
    }
}

class EntityDeclaration {
    public $extension_class = null;
    private $fields = array();
    private $relationships = array();

    public function EntityDeclaration ($name){
        $this->class = $name;
        $this->table = new TableMetadata($name);
        $this->validator = new EntityValidator($name);
    }
    public function have($fieldname, $field){
        $this->fields[$fieldname] = $field->set_name($fieldname);
        return $this;
    }
    public function has_many($fieldname){
        return $this;
    }
    public function from_entity($entity_class_name){
        $this->entity_class_name = $entity_class_name;
        return $this;
    }
    public function through($m2m_table_name){
        $this->m2m_table_name = $m2m_table_name;
        return $this;
    }
    public function validate_uniqueness_of($fieldname){
        $this->fields[$fieldname]->unique = true;
        return $this;
    }
    public function extend($classname){
        $this->extension_class = $classname;
    }
    public function get_fields(){
        return $this->fields;
    }
}
class TableMetadata {
    public function TableMetadata($name){
        $this->name = $name;
    }
    public function name_is($name) {
        $this->name = $name;
        return $this;
    }
}
class EntityValidator {
    public function EntityValidator($name){
        $this->class = $name;
    }
}

class BaseEntity {
    private static function __get_declaration(){
        $klass = get_called_class();
        return $GLOBALS["__little_joy_entities__"][$klass];
    }
    public static function meta($data){
        $klass = get_called_class();
        switch ($data) {
        case table_name:
            return $klass::__get_declaration()->table->name;
            break;
        default:
            break;
        }
    }
    public static function sql_for($what){
        $klass = get_called_class();
        $sql = new SQLManager($klass::__get_declaration());
        return $sql->$what();
    }
}
function Entity($klass, $creation_callback){
    $name = "$klass";
    $success = false;
    if (class_exists($name)){
        unset($name);
    }
    @eval("class $name extends BaseEntity {}");

    if (is_subclass_of($klass, "BaseEntity")) {
        $success = true;
    }
    $declaration = new EntityDeclaration($klass);
    $GLOBALS["__little_joy_entities__"][$klass] = $declaration;

    $does = $declaration;
    $validate = $declaration->validator;
    $table = $declaration->table;

    $creation_callback($does, $validate, $table);

    return $success;
}

?>