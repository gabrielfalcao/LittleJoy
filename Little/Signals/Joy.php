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
define("EverythingJoy", "EverythingJoy");
define("SignalJoy", "SignalJoy");

class EverythingJoy {
    public $got_fine;
    private $data;
    public function EverythingJoy($got_fine=true, $data=null){
        $this->got_fine = $got_fine;
        $this->data = $data;
    }
    public function &__get($name){
        if (is_array($this->data)) {
            return $this->data[$name];
        }
    }
    public function __isset($name) {
        return isset($this->data[$name]);
    }
}

class ListenerJoy {
    public function ListenerJoy($name, $callback){
        $this->name = $name;
        $this->callback = $callback;
    }
    public function these($things){
        return $this->callback($things);
    }
}

class SignalJoy {
    public function SignalJoy ($name) {
        $this->name = $name;
    }
    public static function get_befores($name){
        $key = "joy-signals-before:$name";
        $list = Joy::get($key, array());
        return $list;
    }
    public static function set_befores($name, $list){
        $key = "joy-signals-before:$name";
        Joy::set($key, $list);
        return $key;
    }

    public static function get_afters($name){
        $key = "joy-signals-after:$name";
        $list = Joy::get($key, array());
        return $list;
    }
    public static function set_afters($name, $list){
        $key = "joy-signals-after:$name";
        Joy::set($key, $list);
        return $key;
    }

    public static function call_listeners ($name, $listeners, $things){
        $signal = new SignalJoy($name);

        foreach ($listeners as $do_these):
            $do_these($things);
        endforeach;
    }
    public static function yell_before_joy($name, $success=true, $data=null){
        $things = new EverythingJoy($success, $data);
        return self::call_listeners($name, SignalJoy::get_befores($name), $things);
    }
    public static function yell_after_joy($name, $success=true, $data=null){
        $things = new EverythingJoy($success, $data);
        return self::call_listeners($name, SignalJoy::get_afters($name), $things);
    }
}

function BeforeJoy($name, $callback){
    $list = SignalJoy::get_befores($name);
    $list []= $callback;
    SignalJoy::set_befores($name, $list);
}
function AfterJoy($name, $callback){
    $list = SignalJoy::get_afters($name);
    $list []= $callback;
    SignalJoy::set_afters($name, $list);
}

?>
