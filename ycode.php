<?php

require_once "will_scan_string.php";

$ycode_global_string_scanner = new WillScanString();
$ycode_nested_string_scanner = new WillScanString();

$ycode_global_string_scanner->register_preg_replacement("/(?:\\A|\\^(\\d))([\\s\\S]*?)(?=\\Z|\\^\\d)/", function($_, $color, $content)
{
	global $ycode_nested_string_scanner;
	return sprintf('<span class="color color%d">%s</span>', intval($color), $ycode_nested_string_scanner->replace($content));
});

foreach(array(
	"/_(.+)_/" => function($_, $content)
	{ var_dump(utf8_html_entities($content)); return sprintf("<strong>%s</strong>", utf8_html_entities($content)); },
	"/^\\/\\/\\s*(.+)/" => function($_, $username)
	{ return sprintf('<a href="user_by_username.php?username=%s">%s</a>', utf8_html_entities(rawurlencode($username)), utf8_html_entities($username)); },
	"/~~~~\\r\\n(.+?)\\r\\n([\\s\\S]*?)\\r\\n~~~~/" => function($_, $quotee, $content)
	{ global $ycode_global_string_scanner;
	  return sprintf('<blockquote><span class="User Name">%s</span>%s</blockquote>', utf8_html_entities($quotee), $ycode_global_string_scanner->replace($content)); },
	"/</" => "&lt;",
	"/>/" => "&gt;",
	"/&/" => "&amp;",
	"/(?:\\r\\n|\\r|\\n)/" => function($_)
	{ return "<br>"; }
) as $pattern => $callback)
{
	$ycode_global_string_scanner->register_preg_replacement($pattern, $callback);
	$ycode_nested_string_scanner->register_preg_replacement($pattern, $callback);
}

function parse_ycode_formatted_string_to_html( $string )
{
	global $ycode_global_string_scanner;
	return $ycode_global_string_scanner->replace($string);
}

?>