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
import("Models/Engine");
import("Models/Entity");

define("table_name", "table_name");
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
    public function save(){
        $klass = get_called_class();
        Joy::query($klass::sql_for(insert, $this));
        $this->ID = mysql_insert_id($this->connection);
    }
    public static function syncdb($overwrite=false){
        $klass = get_called_class();
        $db_table = $klass::meta(table_name);
        if ($overwrite) {
            Joy::query("DROP TABLE IF EXISTS `$db_table`;");
        }
        $results = array();
        foreach (explode(";", $klass::as_table_string()) as $sql):
            array_push($results, array($sql => Joy::query($sql)));
        endforeach;
        return $results;
    }
}

?>
