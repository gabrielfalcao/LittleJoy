<?php
class TestResponseJoy extends PHPUnit_Framework_TestCase {
    public function testFixHeaderKey() {
        $variations = array(
            " content - length ",
            "CONTENT -   lengtH",
            "content_length",
            "Content-Length",
        );
        foreach ($variations as $badkey) {
            $this->assertEquals(ResponseJoy::fix_http_header_key($badkey), "Content-Length");
        }
        $this->assertEquals(count($variations), 4);

    }
}
?>