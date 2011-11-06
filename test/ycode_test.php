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

frseql("should enable bold words", function()
{ return get_stripped_parser_result("_bold!_");
}, "<strong>bold!</strong>");

frseql("should enable color switching", function()
{ return parse_ycode_formatted_string_to_html("black ^1 red ^0 black");
}, '<span class="color color0">black </span><span class="color color1"> red </span><span class="color color0"> black</span>');

frseql("should be able to handle nested patterns", function()
{ return get_stripped_parser_result("black ^1 _bold & red_");
}, 'black </span><span class="color color1"> <strong>bold &amp; red</strong>');

frseql("should be able to link to user profiles", function()
{ return get_stripped_parser_result("check profile of\r\n//tobyhinloopen\r\n\r\nHe's awesome!");
}, 'check profile of<br><a href="user_by_username.php?username=tobyhinloopen">tobyhinloopen</a><br><br>He\'s awesome!');

?>