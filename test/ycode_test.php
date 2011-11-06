<?php

require_once "test_helpers.php";
require_once "ycode.php";

function get_stripped_parser_result( $string )
{
	$result = parse_ycode_formatted_string_to_html( $string );
	if(substr($result, 0, strlen('<span class="color color0">')) == '<span class="color color0">'
	&& substr($result, -strlen("</span>")) == "</span>")
		return substr($result, strlen('<span class="color color0">'), -strlen("</span>"));
	else
		return $result;
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
}, "a<br />b<br />c<br />d");

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
}, 'check profile of<br /><a href="user_by_username.php?username=tobyhinloopen" class="auto-embedded">tobyhinloopen</a><br /><br />He\'s awesome!');

frseql("should convert a url to a link", function()
{ return get_stripped_parser_result("www.google.com http://www.google.com/");
}, '<a href="http://www.google.com" rel="nofollow" class="auto-embedded">[www.google.com]</a> <a href="http://www.google.com/" rel="nofollow" class="auto-embedded">[www.google.com]</a>');

frseql("should embed an image", function()
{ return get_stripped_parser_result("http://i.imgur.com/H5H6f.png");
}, '<img src="http://i.imgur.com/H5H6f.png" class="auto-embedded" />');

frseql("should embed a youtube video", function()
{ return get_stripped_parser_result("http://www.youtube.com/watch?v=-kHzZZvsdOE");
}, '<embed src="http://www.youtube.com/v/-kHzZZvsdOE&fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="583" height="354" class="auto-embedded" />');

frseql("should parse a quote", function()
{ return get_stripped_parser_result("~~~~\r\nRubberEendje\r\nHoi!\r\n~~~~");
}, '<blockquote><span class="User Name">RubberEendje</span><br /><span class="color color0">Hoi!</span></blockquote>');

frseql("should match a color at the start of the string", function()
{ return parse_ycode_formatted_string_to_html("^1red");
}, '<span class="color color1">red</span>');

frseql("should be able to handle colors in a quote", function()
{ return get_stripped_parser_result("~~~~\r\nx\r\n^1red\r\n~~~~black");
}, '<blockquote><span class="User Name">x</span><br /><span class="color color1">red</span></blockquote>black');

frseql("should be able to handle colors in a rauwkost", function()
{ return get_stripped_parser_result("[rauw]^1red[/rauw]black");
}, '<div class="slideContainer"><a href="#" class="ShowHidden">[rauwkost]</a><div class="SlideText"><span class="color color1">red</span><span class="HideHiddenLink"><a href="#" class="HideHidden">[rauwkost]</a></span></div></div>black');


?>