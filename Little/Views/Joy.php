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

import("Views/haml/HamlParser.class");

function render_view($name, $context=null, $basepath=null) {
    if ($basepath && file_exists($basepath)) {
        $viewdir = is_dir($basepath) ? $basepath : dirname($basepath);

    } else {
        $viewdir = $GLOBALS["__little_joy_views_dir__"];
    }

    $fullpath = $viewdir.DIRECTORY_SEPARATOR.trim($name, "/");

    $parser = new HamlParser();

    if (is_array($context)) {
        foreach ($context as $key => $value):
            $parser->assign($key, $value);
            endforeach;
    }

    $parser->setTmp(sys_get_temp_dir());
    $parser->setFile($fullpath);
    $rendered = $parser->render();
    $parser->clearCompiled();
    return trim($rendered);
}
