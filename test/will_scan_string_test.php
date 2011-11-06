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

frseql("should update regular expressions with back references", function()
{
	$ss = new WillScanString();
	$ss->register_preg_replacement_callback("/<([a-z]+)>.*?<\\/\\1>/", function($_, $tagname)
	{ return $tagname; });
	return $ss->get_replacement_pattern();
}, "/(?:(<([a-z]+)>.*?<\\/\\2>))/");

frseql("should perform a regexp replacement with a capture group", function()
{
	$ss = new WillScanString();
	$ss->register_preg_replacement_callback("/<([a-z]+)>.*?<\\/\\1>/", function($_, $tagname)
	{ return $tagname; });
	return $ss->replace("<strong>hi!</strong>");
}, "strong");

?>