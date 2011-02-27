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

import("Views/lessc.inc");
define("LessJoy", "LessJoy");

define("serve_lesscss_from", "serve_lesscss_from");
define("serve_lesscss_on", "serve_lesscss_on");

class LessJoy extends ControllerJoy {
    var $urls = array(
        "(?P<path>.*)[.]css$" => "serve",
    );
    public function serve($response, $params, $route) {
        $fallback = FileSystemJoy::absolute_path("stylesheets");
        $basepath = Joy::get(serve_lesscss_from, $fallback);
        $basepath = FileSystemJoy::turn_array_into_path_if_needed($basepath);
        $fullpath = FileSystemJoy::absolute_path($params['path'].'.less', $basepath);


        $less = new lessc($fullpath);
        $response->set_http_header('Content-Type', 'text/css');
        return $less->parse();
    }
}

?>
