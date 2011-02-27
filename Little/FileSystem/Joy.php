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

class FileSystemJoy {
    public static function turn_array_into_path_if_needed($mixed){
        if (is_array($mixed)){
            $candidate = implode(DIRECTORY_SEPARATOR, $mixed);
            if (!file_exists($candidate) || is_dir($candidate)){
                return $candidate;
            } else {
                return dirname($candidate);
            }
        }
        return $mixed;
    }
    public static function path_from_docroot($path){
        return self::absolute_path($path);
    }
    public static function absolute_path($path=".", $basepath=null){
        if ($basepath == null) {
            $basepath = $_SERVER['DOCUMENT_ROOT'];
        } else if (file_exists($basepath) && !is_dir($basepath)) {
            $basepath = dirname($basepath);
        }
        if ($path == ".") {
            $path = "";
        }
        if (is_array($path)) {
            $path = self::turn_array_into_path_if_needed($path);
        }

        $ret = $basepath.DIRECTORY_SEPARATOR.$path;

        return preg_replace(','.DIRECTORY_SEPARATOR.'+,', DIRECTORY_SEPARATOR, $ret);
    }
}

?>
