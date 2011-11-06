<?php

require_once "test_helpers.php";
require_once "will_scan_string.php";

frseql("should perform a regular replacement", function()
{
	$ss = new WillScanString();
	$ss->register_replacement(":)", "HAPPY");
	return $ss->replace(":)");
}, "HAPPY");

frseql("should update regular expressions with back references", function()
{
	$ss = new WillScanString();
	$ss->register_preg_replacement("/<([a-z]+)>.*?<\\/\\1>/", function($_, $tagname)
	{ return $tagname; });
	return $ss->get_replacement_pattern();
}, "/(?:(<([a-z]+)>.*?<\\/\\2>))/");

frseql("should perform a regexp replacement with a capture group", function()
{
	$ss = new WillScanString();
	$ss->register_preg_replacement("/<([a-z]+)>.*?<\\/\\1>/", function($_, $tagname)
	{ return $tagname; });
	return $ss->replace("<strong>hi!</strong>");
}, "strong");

frseql("should not replace replaced strings", function()
{
	$ss = new WillScanString();
	$ss->register_replacement(":)", '<img src="happy.png" alt=":)" title=":)">');
	$ss->register_replacement("<", "&lt;");
	$ss->register_replacement(">", "&gt;");
	$ss->register_replacement("\"", "&quot;");
	$ss->register_replacement("&", "&amp;");
	return $ss->replace("& :)");
}, '&amp; <img src="happy.png" alt=":)" title=":)">');

frseql("should be able to use multiple regular expressions to replace with", function()
{
	$ss = new WillScanString();
	$ss->register_preg_replacement("/(a)(b)/", "AB");
	$ss->register_preg_replacement("/(c)(d)/", "CD");
	return $ss->replace("abcd");
}, "ABCD");

frseql("should be able to match newlines", function()
{
	$ss = new WillScanString();
	$ss->register_replacement("\r\n", "<br>");
	return $ss->replace("\r\n");
}, "<br>");

frseql("should be able to match newlines using a regexp", function()
{
	$ss = new WillScanString();
	$ss->register_preg_replacement("/\\r\\n/", "<br>");
	return $ss->replace("\r\n");
}, "<br>");

?>