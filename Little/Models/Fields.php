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

/**
 * LittleJoy's fields file,
 *
 * this file holds all the field types and sets several constants to
 * turn the field declaration syntax a LOT sweeter.
 *
 * @author Gabriel Falcão <gabriel@nacaolivre.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 0.1
 * @package LittleJoy
 */

/**#@+
 * Constants
 */
/**
 * The field type, MUST be used in a key-value array when declaring
 * fields into ModelJoy classes.
 *
 * Example:
 *     array(..., type => CharField)
 *
 */
define("type", "type");
/**
 * Used to explicitly set the actual db_column name for the given
 * field, MUST be used in a key-value array when declaring
 * fields into ModelJoy classes.
 *
 * Example:
 *     array(..., db_column => "name_of_the_field")
 *
 */
define("db_column", "db_column");
/**
 * Used to explicitly set the actual max_length name for the given
 * field, MUST be used in a key-value array when declaring
 * fields into ModelJoy classes.
 *
 * If not set, the default value for the given field type will be used
 *
 * Example:
 *     array(..., max_length => "name_of_the_field")
 *
 */
define("max_length", "max_length");
/**
 * Used to explicitly set if the field if NULL or NOT NULL for the given
 * field, MUST be used in a key-value array when declaring
 * fields into ModelJoy classes.
 *
 * If not set, LittleJoy will assume "false" (NOT NULL)
 *
 * Example:
 *     array(..., nullable => true)
 *
 */
define("nullable", "nullable");
/**
 * Used to set a array of string choices on ChoiceField
 *
 * Obligatory
 *
 * Example:
 *     array(..., choices => array("banana", "orange"))
 *
 */
define("choices", "choices");
/**
 * Used to set the name of the classe related with a ForeignKey or ManyToManyField
 *
 * Obligatory
 *
 * Example:
 *     array(..., related_with => "Foobar")
 *
 */
define("related_with", "related_with");




class NotImplemented extends Exception {}
$GLOBALS["__little_joy_field_types__"] = array(
    "CharField" => "varchar",
    "EmailField" => "varchar",
    "URLField" => "varchar",
    "ChoiceField" => "varchar",

    "AutoField" => "integer",
    "ForeignKey" => "integer",
    "ManyToManyField" => "integer",
    "IntegerField" => "integer",

    "TextField" => "longtext",

    "DateTimeField" => "datetime",
);
foreach ($GLOBALS["__little_joy_field_types__"] as $name => $type) {
    define($name, $name);
}

abstract class Field {
    protected function get_type($declaration){
        return $GLOBALS["__little_joy_field_types__"][$declaration[type]];
    }

    function Field($name, $db_column, $nullable=false,
                         $declaration=null){
        $this->name = $name;
        $this->db_column = $db_column;
        $this->type = $this->get_type($declaration);
        $this->nullable = $nullable;

    }
    public static function from_declaration_array($name, $declaration){
        $db_column = $name;
        $nullable = false;

        if (array_key_exists(db_column, $declaration)):
            $db_column = $declaration[db_column];
        endif;
        if (array_key_exists(nullable, $declaration)):
            $nullable = $declaration[nullable];
        endif;

        $klass = get_called_class();
        return new $klass($name, $db_column, $nullable, $declaration);
    }
    protected function get_column_string(){
        return $this->type;
    }
    protected function get_final_params(){
        return $this->nullable ? "NULL" : "NOT NULL";
    }

    public function as_string(){
        return $this->get_column_string() . " " . $this->get_final_params();
    }
}

class CharField extends Field {
    var $default_max_length = 255;
    function CharField($name, $db_column, $nullable=false,
                         $declaration=null){
        parent::Field($name, $db_column, $nullable, $declaration);

        $this->max_length = array_key_exists(max_length, $declaration) ?
            $declaration[max_length] : $this->default_max_length;

    }
    protected function get_column_string(){
        return parent::get_column_string()."($this->max_length)";
    }


}
class URLField extends CharField {}
class EmailField extends CharField {}

class IntegerField extends Field {}
class ForeignKey extends IntegerField {}
class ManyToManyField extends Field {}

class DateTimeField extends Field {}
class TextField extends Field {}

class ChoiceField extends CharField {
    function ChoiceField($name, $db_column, $nullable=false,
                         $declaration=null){
        parent::CharField($name, $db_column, $nullable, $declaration);

        if (!in_array(choices, $declaration)){
            throw new Exception("The ChoiceField $name needs a 'choices' declaration, an array with choices as strings");
        }
        $this->choices = $declaration[choices];
        $this->max_length = max(array_map("strlen", $this->choices));
    }

}


?>
