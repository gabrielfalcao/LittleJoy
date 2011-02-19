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

function import($path, $absolute_to_joy=true) {
    $parts = explode("/", $path);
    if ($absolute_to_joy){
        $here = dirname(realpath(__FILE__));
        array_unshift($parts, $here);
    }
    require_once join(DIRECTORY_SEPARATOR, $parts).".php";
}

import("DB/Joy");

class Joy {
    public static function version (){
        return "0.1";
    }
}

?>
