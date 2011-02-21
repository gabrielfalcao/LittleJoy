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
 * LittleJoy's main file,
 *
 * require this one, and you have everything :)
 *
 * @author Gabriel Falcão <gabriel@nacaolivre.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 0.1
 * @package LittleJoy
 */
/**
 *  Takes a unix-like path to file and require it properly with
 *  require_once. The last path element with be appended with ".php" implicitly.
 *
 *  Example:
 *
 *  import("Contrib/Admin"); will require_once("/absolute/path/to/Little/Contrib/Admin.php");
 *
 *  import("module/file", false); will require_once("module/file.php");
 *
 *  on windows, it becomes require_once("C:\\absolute\path\to\Little\Contrib\Admin.php");
 *  and so on...
 *
 *  @param string $path The unix-like path
 *  @param bool $absolute_to_joy if the import should be prepended by the absolute path to LittleJoy's root dir. Defaults to true
 */

function import($path, $absolute_to=__FILE__) {
    $parts = explode("/", $path);
    array_unshift($parts, dirname(realpath($absolute_to)));

    require_once join(DIRECTORY_SEPARATOR, $parts).".php";
}

import("DB/Joy");
import("Controllers/Joy");

class DatabaseDoesNotExist extends Exception {}
define("DatabaseDoesNotExist", "DatabaseDoesNotExist");
class CouldNotConnectToDatabase extends Exception {}
define("CouldNotConnectToDatabase", "CouldNotConnectToDatabase");
class NoDatabaseRegistered extends Exception {}
define("NoDatabaseRegistered", "NoDatabaseRegistered");

class Joy {
    public static function version (){
        return "0.1";
    }
    public static function set_mysql_database($host, $user, $password, $database){
        $GLOBALS["__little_joy_mysql_user__"] = $user;
        $GLOBALS["__little_joy_mysql_host__"] = $host;
        $GLOBALS["__little_joy_mysql_password__"] = $password;
        $GLOBALS["__little_joy_mysql_database__"] = $database;

    }
    public static function connect_to_mysql_database($host, $user, $password, $database){
        self::set_mysql_database($host, $user, $password, $database);
        return self::connect_to_registered_database();
    }

    public static function disconnect_from_registered_database(){
        mysql_close($GLOBALS["__little_joy_mysql_connection__"]);
        unset($GLOBALS["__little_joy_mysql_connection__"]);

    }
    public static function connect_to_registered_database(){
        if (!array_key_exists("__little_joy_mysql_host__", $GLOBALS)) {
            throw new NoDatabaseRegistered("Joy::set_mysql_database must be called before Joy::connect_to_registered_database");
        }
        $user = $GLOBALS["__little_joy_mysql_user__"];
        $host = $GLOBALS["__little_joy_mysql_host__"];
        $password = $GLOBALS["__little_joy_mysql_password__"];
        $database = $GLOBALS["__little_joy_mysql_database__"];

        $c = mysql_connect($host, $user, is_null($password) ? "": $password);
        if (!$c) {
            throw new CouldNotConnectToDatabase("Could not connect to the database $user:$password@$host");
        }
        $GLOBALS["__little_joy_mysql_connection__"] = $c;
        mysql_set_charset("utf-8", $c);
        if (mysql_select_db($database, $c)) {
            return $c;
        } else {
            throw new DatabaseDoesNotExist("The database \"$database\" does not exist!". mysql_error());
        }
    }
    public static function get_current_mysql_connection() {
            if (!array_key_exists("__little_joy_mysql_connection__", $GLOBALS)) {
                throw new Exception("You must call Joy::connect_to_mysql_database() before calling any Model::syncdb()");
            }
            return $GLOBALS["__little_joy_mysql_connection__"];
    }
    public static function syncdb() {
        $con = self::connect_to_registered_database();
        foreach (get_declared_classes() as $klass):
            $parent = get_parent_class($klass);
            if ($parent == "ModelJoy") {
                $klass::syncdb();
            }
        endforeach;
        self::disconnect_from_registered_database();
    }
    public static function and_work() {
        $route = RouteJoy::resolve($_SERVER["REQUEST_URI"]);
        $response = new ResponseJoy(200);
        echo $route->process($response);
    }
    public static function find_templates_at($base, $postpath){
        $path = dirname($base).DIRECTORY_SEPARATOR.$postpath;
        $GLOBALS["__little_joy_views_dir__"] = $path;
    }
}

?>
