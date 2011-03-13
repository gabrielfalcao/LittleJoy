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

    $to_import = join(DIRECTORY_SEPARATOR, $parts).".php";

    $overjoyed = preg_replace(',[.]php$,', DIRECTORY_SEPARATOR.'Joy.php', $to_import);
    if (file_exists($overjoyed) && is_file($overjoyed)) {
        require_once $overjoyed;
    } else {
        require_once $to_import;
    }
}
import("Signals");
import("FileSystem");
import("Models");
import("Controllers");
import("Views");

class DatabaseDoesNotExist extends Exception {}
define("DatabaseDoesNotExist", "DatabaseDoesNotExist");
class CouldNotConnectToDatabase extends Exception {}
define("CouldNotConnectToDatabase", "CouldNotConnectToDatabase");
class NoDatabaseRegistered extends Exception {}
define("NoDatabaseRegistered", "NoDatabaseRegistered");

$GLOBALS["__little_joy_config__"] = array();

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
    public static function query($SQL, $connection=null){
        return mysql_query($SQL, $connection ? $connection : Joy::get_current_mysql_connection());
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

        $c = @mysql_connect($host, $user, is_null($password) ? "": $password);
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
    public static function enjoy_models(){
        $models = array();
        foreach (get_declared_classes() as $klass):
            $parent = get_parent_class($klass);
            if ($parent == "ModelJoy") {
                array_push($models, $klass);
            }
        endforeach;
        return $models;
    }

    public static function syncdb() {
        $con = self::connect_to_registered_database();
        foreach (self::enjoy_models() as $model):
            $model::syncdb();
        endforeach;
        self::disconnect_from_registered_database();
    }
    private static function _store_work_params($params){
        foreach ($params as $key => $value){
            self::set($key, $value);
        }
    }
    public static function and_work($params=null) {
        if (is_array($params)){
            self::_store_work_params($params); /////////////////////////// <<<<<<------
        } else if ($params != null){
            $_kind = gettype($params);
            throw new Exception("Joy::and_work() takes a keyword based array with startup options, got \"$_kind\"");
        }
        $request_path = $_SERVER["REQUEST_URI"];
        if (array_key_exists('QUERY_STRING', $_SERVER)) {
            $qs = preg_quote($_SERVER['QUERY_STRING']);
            $request_path = preg_replace(",[?]$qs$,", "", $request_path);
        }
        $route = RouteJoy::resolve($request_path);
        $response = new ResponseJoy(200);
        echo trim($route->process($response));
    }
    public static function find_templates_at($base, $postpath){
        $path = dirname($base).DIRECTORY_SEPARATOR.$postpath;
        $GLOBALS["__little_joy_views_dir__"] = $path;
    }
    public static function get($key, $fallback=null){
        if (!array_key_exists($key, $GLOBALS["__little_joy_config__"])){
            return $fallback;
        }
        return $GLOBALS["__little_joy_config__"][$key];
    }
    public static function set($key, $value){
        $GLOBALS["__little_joy_config__"][$key] = $value;
        return $GLOBALS["__little_joy_config__"];
    }
    public static function debug_as_json($json)
    {
        $tab = "  ";
        $new_json = "";
        $indent_level = 0;
        $in_string = false;
        if (is_array($json)){
            $json_obj = $json;
        } else {
            $json_obj = json_decode($json);
        }

        if($json_obj === false)
            return false;

        $json = json_encode($json_obj);
        $len = strlen($json);

        for($c = 0; $c < $len; $c++)
            {
                $char = $json[$c];
                switch($char)
                    {
                    case '{':
                    case '[':
                        if(!$in_string)
                            {
                                $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                                $indent_level++;
                            }
                        else
                            {
                                $new_json .= $char;
                            }
                        break;
                    case '}':
                    case ']':
                        if(!$in_string)
                            {
                                $indent_level--;
                                $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                            }
                        else
                            {
                                $new_json .= $char;
                            }
                        break;
                    case ',':
                        if(!$in_string)
                            {
                                $new_json .= ",\n" . str_repeat($tab, $indent_level);
                            }
                        else
                            {
                                $new_json .= $char;
                            }
                        break;
                    case ':':
                        if(!$in_string)
                            {
                                $new_json .= ": ";
                            }
                        else
                            {
                                $new_json .= $char;
                            }
                        break;
                    case '"':
                        if($c > 0 && $json[$c-1] != '\\')
                            {
                                $in_string = !$in_string;
                            }
                    default:
                        $new_json .= $char;
                        break;
                    }
            }

        return $new_json;
    }
}

?>
