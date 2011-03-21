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

define("column_name", "column_name");
define("max_length", "max_length");
define("nullable", "nullable");
define("choices", "choices");
define("related_with", "related_with");

class NotImplemented extends Exception {}

abstract class Field{
    protected $db_type = null;
    public $unique = false;

    protected $name = "";
    protected $nullable = false;
    protected $declaration = array();
    public function Field($declaration=array()){
        $this->declaration = $declaration;
    }
    public function is_nullable() {
        return Joy::array_get($this->declaration, nullable, $this->nullable);
    }
    public function set_name($name) {
        $this->name = $name;
        return $this;
    }
    protected function get_column_name(){
        return Joy::array_get(
            $this->declaration, column_name,
            $this->name
        );
    }
    protected function get_column_type(){
        return $this->db_type;
    }
    public function as_sql() {
        $klass = get_class($this);
        $colname = $this->get_column_name();
        $coltype = $this->get_column_type();
        $isnull = $this->is_nullable() ? "NULL" : "NOT NULL";
        return "  `{$colname}` {$coltype} {$isnull},";
    }
}

class CharField extends Field {
    protected $db_type = "varchar";
    public $max_length = 255;
    function CharField($max_length=null, $declaration=array()){
        parent::Field($declaration);
        if (!is_null($max_length)) {
            $this->max_length = $max_length;
        }
    }
    protected function get_column_type(){
        return parent::get_column_type()."({$this->max_length})";
    }
}
class URLField extends CharField {}
class EmailField extends CharField {
    public $max_length = 75;
}

class BooleanField extends Field {protected $db_type = "tinyint(1)";}
class IntegerField extends Field {protected $db_type = "int";}
class ForeignKey extends IntegerField {}
class ManyToManyField extends Field {}

class DateField extends Field {protected $db_type = "date";}
class TimeField extends Field {protected $db_type = "time";}
class DateTimeField extends Field {protected $db_type = "datetime";}

class TextField extends Field {protected $db_type = "longtext";}

class ChoiceField extends CharField {
    function ChoiceField($name, $db_column, $nullable=false,
                         $declaration=array()){
        parent::CharField($name, $db_column, $nullable, $declaration);

        if (!in_array(choices, $declaration)){
            throw new Exception("The ChoiceField $name needs a 'choices' declaration, an array with choices as strings");
        }
        $this->choices = $declaration[choices];
        $this->max_length = max(array_map("strlen", $this->choices));
    }

}


?>
