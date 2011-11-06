<?php

require_once "test_helpers.php";
require_once "will_scan_string.php";

frseql("should perform a regular replacement", function()
{
	$ss = new WillScanString();
	$ss->register_preg_replacement_callback("/:\\)/", function()
	{ return "HAPPY"; });
	return $ss->replace(":)");
}, "HAPPY");

frseql("should perform a regexp replacement with a capture group", function()
{
	$ss = new WillScanString();
	$ss->register_preg_replacement_callback("/<([a-z]+)>.*?<\\/\\1>/", function($_, $tagname)
	{ return $tagname; });
	die($ss->get_replacement_pattern());
	return $ss->replace("<strong>hi!</strong>");
}, "strong");

?>