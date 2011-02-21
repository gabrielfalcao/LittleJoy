<?php
require_once("Little/Joy.php");

class SomeFooBarController extends ControllerJoy {
    var $urls = array(
        "^$" => 'index',
        "^foo" => 'goo_foo',
        "^bar" => 'bar_method',
        "^fail/me" => 'fail_me'
    );
    public function index ($response, $params) {return 'Got the index';}
    public function goo_foo ($response, $params) {return 'Goo Foo Yeah!';}
    public function bar_method ($response, $params) {return 'Thaz jus bar bro!';}
    public function fail_me ($response, $params) {throw new Exception('Server got screwed!');}
}

class AnotherController extends ControllerJoy {
    var $urls = array(
        "^another$" => 'index',
        "^and/other/?$" => 'other',
        "^last/$" => 'last_freak',
    );
    public function index ($response, $params) {return 'ANOTHER THING';}
    public function other ($response, $params) {return 'Got the other';}
    public function last_freak ($response) {
        $response->set_http_status(304);
        return 'well mocked :)';
    }

}

class TestControllerJoy extends PHPUnit_Framework_TestCase {
   public function testGetRouteFor(){
        $route = SomeFooBarController::get_route_for("/");
        $this->assertEquals(get_class($route->controller), "SomeFooBarController");
        $this->assertEquals($route->controller_method, "index");
        $this->assertEquals($route->mapped_url, "^$");
        $this->assertEquals($route->regex, ",^/$,");
        $this->assertEquals(count($route->matches), 1);
    }
    public function testRouteResolve(){
        $route = RouteJoy::resolve("/");
        $this->assertEquals(get_class($route->controller), "SomeFooBarController");
        $this->assertEquals($route->controller_method, "index");
        $this->assertEquals($route->mapped_url, "^$");
        $this->assertEquals($route->regex, ",^/$,");
        $this->assertEquals(count($route->matches), 1);
    }

    public function testResponseString(){
        $route = RouteJoy::resolve("/");
        $this->assertEquals($route->process(NULL), "Got the index");
    }
    public function testResponse404(){
        $response = $this->getMock('ResponseJoy', array("set_http_status"), array(200));
        $response->expects($this->any())
            ->method('set_http_status')
            ->with(404);


        $route = RouteJoy::resolve("/this/path/does-not-exist");
        $this->assertEquals(get_class($route->controller), "ControllerJoy");
        $this->assertEquals($route->controller_method, "handle_404");
        $this->assertEquals($route->mapped_url, null);
        $this->assertEquals($route->regex, null);

        $this->assertEquals($route->process($response), "404 Not Found");
    }

    public function testResponse500(){
        $response = $this->getMock('ResponseJoy', array("set_http_status"), array(200));
        $response->expects($this->any())
            ->method('set_http_status')
            ->with(500);


        $route = RouteJoy::resolve("/fail/me");
        $this->assertEquals($route->process($response), "500 Internal Server Error");
        $this->assertEquals(get_class($route->controller), "ControllerJoy");
        $this->assertEquals($route->controller_method, "handle_500");
        $this->assertEquals($route->mapped_url, "^fail/me");
        $this->assertEquals($route->regex, ",^/fail/me/?$,");
    }

    public function testResponseArray(){
        $response = $this->getMock('ResponseJoy', array("set_http_status"), array(200));
        $response->expects($this->any())
            ->method('set_http_status');

        $route = RouteJoy::resolve("/last/");
        $this->assertEquals($route->process($response), "well mocked :)");
    }
    public function testRegexFixAppendOptionalSlash(){
        $route = AnotherController::get_route_for("/another");
        $this->assertEquals($route->controller_method, "index");
        $this->assertEquals($route->mapped_url, "^another$");
        $this->assertEquals($route->regex, ",^/another/?$,");
    }
    public function testRegexFixAppendIgnoreExplicitOptionalSlash(){
        $route1 = AnotherController::get_route_for("/and/other");
        $this->assertEquals($route1->controller_method, "other");
        $this->assertEquals($route1->mapped_url, "^and/other/?$");
        $this->assertEquals($route1->regex, ",^/and/other/?$,");

        $route2 = AnotherController::get_route_for("/and/other/");
        $this->assertEquals($route2->controller_method, "other");
    }
}

?>