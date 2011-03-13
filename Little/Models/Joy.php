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
import("Models/Fields");

/**
 * LittleJoy's base model,
 *
 * this file holds the ModelJoy class, that maps PHP classes into
 * MySQL tables, supporting many kinds of fields, DDL and DML
 * rendering, and active record with automatic validation.
 *
 * @author Gabriel Falcão <gabriel@nacaolivre.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 0.1
 * @package LittleJoy
 */

class ModelJoy {
    public function prepare_insert(){
        $klass = get_class($this);
        $fields = "";
        $values = "";
        foreach ($klass::get_fields() as $field_name => $data) {
            $field = $data['field'];
            $value = $this->$field_name;
            $fields .= "`{$field->db_column}`, ";
            $values .= "'{$value}', ";
        }
        $fields = substr($fields, 0, -2);
        $values = substr($values, 0, -2);

        return "INSERT INTO `{$klass}` ({$fields}) VALUES ($values);";
    }
    public function prepare_update(){
        $klass = get_class($this);
        $fields = "";
        foreach ($klass::get_fields() as $field_name => $data) {
            $field = $data['field'];
            $value = $this->$field_name;
            $fields .= "`{$field->db_column}` = '{$value}', ";
        }
        $fields = substr($fields, 0, -2);

        return "UPDATE `{$klass}` SET {$fields} WHERE ID = {$this->ID};";
    }
    public function save(){
        $this->connection = Joy::connect_to_registered_database();
        if (!array_key_exists("ID", get_object_vars($this))) {
            mysql_query($this->prepare_insert(), $this->connection);
            $this->ID = mysql_insert_id($this->connection);
        } else {
            mysql_query($this->prepare_update(), $this->connection);
        }
    }
    public static function __callStatic($name, $arguments) {
        $db_table = get_called_class();
        $matches = array();
        if (preg_match("/^find_one_by_(.*)/", $name, $matches)){
            $res = mysql_query("SELECT * FROM `$db_table` WHERE `{$matches[1]}` = '{$arguments[0]}'", Joy::get_current_mysql_connection());
            if (!$res) {
                return null;
            }
            return self::populated_with(mysql_fetch_assoc($res));
        } else {
            return parent::__callStatic($name, $arguments);
        }
    }

    public static function populated_with($data){
        $klass = get_called_class();
        $object = new $klass();
        foreach ($data as $key => $value) {
            $object->$key = $value;
        }
        return $object;
    }
    public static function get_fields(){
        $klass = get_called_class();
        $fields = array();
        foreach (get_class_vars($klass) as $field_name => $declaration):
            $field = $declaration[type]::from_declaration_array($field_name,
                                                                $declaration);
            $field_type = $declaration[type];
            $fields[$field_name] = array(
                "field_name" => $field_name,
                "field" => $field,
                "field_type" => $field_type,
                "declaration" => $declaration
            );
        endforeach;
        return $fields;
    }
    /**
     * Returns a DDL string for creating the table in MySQL
     * @return string
     */
    public static function as_table_string(){
        $klass = get_called_class();
        $db_table = get_called_class();
        $parts = array();
        array_push($parts, "CREATE TABLE `".$db_table."`(");
        array_push($parts, "    `ID` integer AUTO_INCREMENT NOT NULL PRIMARY KEY,");

        foreach (get_class_vars($klass) as $field_name => $declaration):
            $field = $declaration[type]::from_declaration_array($field_name,
                                                                $declaration);
            if ($declaration[type] == ManyToManyField) {
                continue;
            }
            $column = $field->as_string();
            array_push($parts, "    `$field_name` $column,");

        endforeach;

        end($parts);
        $last_key = key($parts);
        $parts[$last_key] = rtrim($parts[$last_key], ",");

        array_push($parts, ");");

        foreach (get_class_vars($klass) as $field_name => $declaration):
            $field = $declaration[type]::from_declaration_array($field_name,
                                                                $declaration);
            $field_type = $declaration[type];

            switch($field_type) {
            case ForeignKey:
                $related_with = $declaration[related_with];
                array_push($parts,
                           "ALTER TABLE `$db_table` ADD CONSTRAINT `".
                           $klass.
                           "__related_with__".
                           $related_with.
                           "__as__".
                           $field_name.
                           "` FOREIGN KEY (`".
                           $field_name.
                           "`) REFERENCES `".
                           $related_with.
                           "` (`ID`);");
                break;
            case ManyToManyField:
                $related_with = $declaration[related_with];
                $m2m_name = $related_with.$klass."_M2M";

                array_push($parts,
                           "CREATE TABLE `$m2m_name`(");
                array_push($parts,
                           "    `ID` integer AUTO_INCREMENT NOT NULL PRIMARY KEY,");
                array_push($parts,
                           "    `".$related_with."__ID` integer NOT NULL,");
                array_push($parts,
                           "    `".$klass."__ID` integer NOT NULL\n);");
                array_push($parts,
                           "ALTER TABLE `$m2m_name` ADD CONSTRAINT `".
                           $related_with.$klass.
                           "_M2M".
                           "` FOREIGN KEY (`".
                           $related_with."__ID`".
                           ") REFERENCES `".
                           $related_with.
                           "` (`ID`);");
                array_push($parts,
                           "ALTER TABLE `$m2m_name` ADD CONSTRAINT `".
                           $klass.$related_with.
                           "_M2M".
                           "` FOREIGN KEY (`".
                           $klass."__ID`".
                           ") REFERENCES `".
                           $klass.
                           "` (`ID`);");

                break;
            }
        endforeach;

        return implode("\n", $parts);
    }
    public static function syncdb($overwrite=false){
        $klass = get_called_class();
        $db_table = get_called_class();
        if ($overwrite) {
            Joy::query("DROP TABLE IF EXISTS `$db_table`;");
        }
        Joy::query($klass::as_table_string());
    }
}

?>
