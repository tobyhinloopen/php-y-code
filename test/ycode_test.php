<?php

require_once "test_helpers.php";
require_once "ycode.php";

function get_stripped_parser_result( $string )
{
	$result = parse_ycode_formatted_string_to_html( $string );
	return substr($result, strlen('<span class="color color0">'), -strlen("</span>"));
}

frseql("get_stripped_parser_result", function()
{ return get_stripped_parser_result("banana");
}, "banana");

frseql("should wrap color0 spans around the string", function()
{ return parse_ycode_formatted_string_to_html("banana");
}, '<span class="color color0">banana</span>');

frseql("should replace html entities", function()
{ return get_stripped_parser_result("&<>");
}, "&amp;&lt;&gt;");

frseql("should replace new-lines to <br>'s", function()
{ return get_stripped_parser_result("a\r\nb\rc\nd");
}, "a<br>b<br>c<br>d");

?>