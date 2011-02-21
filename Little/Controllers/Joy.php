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
import("haml/HamlParser.class");

class IncompleteControllerError extends Exception {}
define("IncompleteControllerError", "IncompleteControllerError");

class WrongURLSet extends Exception {}
define("WrongURLSet", "WrongURLSet");

define("ResponseJoy", "ResponseJoy");
define("ControllerJoy", "ControllerJoy");
define("RouteJoy", "RouteJoy");

$STATUSES = array(
    100 => "Continue",
    101 => "Switching Protocols",
    200 => "OK",
    201 => "Created",
    202 => "Accepted",
    203 => "Non-Authoritative Information",
    204 => "No Content",
    205 => "Reset Content",
    206 => "Partial Content",
    300 => "Multiple Choices",
    301 => "Moved Permanently",
    302 => "Found",
    303 => "See Other",
    304 => "Not Modified",
    305 => "Use Proxy",
    307 => "Temporary Redirect",
    400 => "Bad Request",
    401 => "Unauthorized",
    402 => "Payment Required",
    403 => "Forbidden",
    404 => "Not Found",
    405 => "Method Not Allowed",
    406 => "Not Acceptable",
    407 => "Proxy Authentication Required",
    408 => "Request Time-out",
    409 => "Conflict",
    410 => "Gone",
    411 => "Length Required",
    412 => "Precondition Failed",
    413 => "Request Entity Too Large",
    414 => "Request-URI Too Large",
    415 => "Unsupported Media Type",
    416 => "Requested range not satisfiable",
    417 => "Expectation Failed",
    500 => "Internal Server Error",
    501 => "Not Implemented",
    502 => "Bad Gateway",
    503 => "Service Unavailable",
    504 => "Gateway Time-out",
    505 => "HTTP Version not supported",
);
class ResponseJoy {
    public function ResponseJoy ($status_code){
        $this->status = intval($status_code);
    }
    public function set_http_status($code){
        $this->status = intval($code);
        global $STATUSES;
        $meaning = $STATUSES[$this->status];
        $this->set_http_header("Status: {$this->status} $meaning", true, $this->status);
    }
    public static function fix_http_header_key($name){
        $name = str_replace(" ", "", trim($name));
        $name = str_replace("_", "-", trim($name));
        return implode("-", array_map("ucfirst", array_map("strtolower", explode("-", $name))));
    }
    public function set_http_header($pre_key, $pre_value){
        $key = self::fix_http_header_key($pre_key);
        $value = trim($pre_value);
        header("$key: $value", true, $this->status);
    }
}
class RouteJoy {
    public function RouteJoy($controller, $controller_method, $matches,
                             $regex, $mapped_url) {
        $this->controller = $controller;
        $this->controller_method = $controller_method;
        $this->matches = $matches;
        $this->mapped_url = $mapped_url;
        $this->regex = $regex;
    }
    public function process($response){
        $method = $this->controller_method;
        try {
            return $this->controller->$method($response, $this->matches, $this);
        } catch (Exception $e) {
            $ctrl = new ControllerJoy();
            $this->controller = $ctrl;
            $this->controller_method = "handle_500";
            return $ctrl->handle_500($response, $this->matches, $this, $e);
        }
    }
    public static function resolve($request_uri) {
        $route = null;
        foreach (get_declared_classes() as $klass):
            $parent = get_parent_class($klass);
            if ($parent == "ControllerJoy") {
                $candidate = $klass::get_route_for($request_uri);
                if ($candidate && get_class($candidate) == RouteJoy){
                    $route = $candidate;
                    break;
                }
            }
        endforeach;
        if (!$route) {
            $ctrl = new ControllerJoy();
            $route = new RouteJoy($ctrl, "handle_404", null, null, null);
        }
        return $route;
    }
}

class ControllerJoy {
    public function handle_404($response, $matches, $route) {
        $response->set_http_status(404);
        return '404 Not Found';
    }
    public function handle_500($response, $matches, $route, $exception) {
        $response->set_http_status(500);
        return '500 Internal Server Error';
    }
    public function render($name, $context=null) {
        $viewdir = $GLOBALS["__little_joy_views_dir__"];
        $fullpath = $viewdir.DIRECTORY_SEPARATOR.trim($name, "/");

        $parser = new HamlParser(false, false);

        if (is_array($context)) {
            foreach ($context as $key => $value):
                $parser->assign($key, $value);
            endforeach;
        }

        $parser->setTmp(sys_get_temp_dir());
        $parser->setFile($fullpath);
        $rendered = @$parser->render();
        @$parser->clearCompiled();
        return $rendered;
    }
    public static function fix_regex($pre_regex) {
        // fixing starts
        $pre_regex = ltrim($pre_regex, "^/");

        // remove leading explicit end
        $pre_regex = preg_replace(",(/[?])?[$]*$,", "", $pre_regex);

        // remove leading explicit end
        $pre_regex = preg_replace(",([^/]+)$,", '${1}/?', $pre_regex);

        return ",^/{$pre_regex}\$,";
    }
    public static function get_route_for($request_uri) {
        $klass = get_called_class();

        /* skipping myself */
        if ($klass == "ControllerJoy") {return;}

        $members = get_class_vars($klass);
        $methods = get_class_methods($klass);

        if (!array_key_exists("urls", $members)) {
            throw new IncompleteControllerError("The controller $klass should implement the class attribute \$urls");
        }

        $ctrl = new $klass();
        foreach ($ctrl->urls as $pre_regex => $callable_name):
            // some validation
            if (!in_array($callable_name, $methods)){
                throw new WrongURLSet("The controller $klass is mapping the url \"$pre_regex\" to the method \"$callable_name\" which is not declared within it");
            }

            $mapped_url = $pre_regex;
            $regex = self::fix_regex($pre_regex);
            $matches = array();
            if (preg_match($regex, $request_uri, $matches)) {
                return new RouteJoy($ctrl, $callable_name, $matches,
                                    $regex, $mapped_url);
            }
        endforeach;
    }
}

?>
