<?php

require_once dirname(__FILE__)."/will_scan_string.php";

$GLOBALS["ycode_global_string_scanner"] = new WillScanString();
$GLOBALS["ycode_nested_string_scanner"] = new WillScanString();

foreach(array(
	"/_(.+)_/" => function($_, $content)
	{ return sprintf("<strong>%s</strong>", utf8_html_entities($content)); },
	"/(?<=\\A|\\r\\n|\\r|\\n)\\/\\/\\s*(.+)/" => function($_, $username)
	{ return sprintf('<a href="user_by_username.php?username=%s" class="auto-embedded">%s</a>', utf8_html_entities(rawurlencode(trim($username))), utf8_html_entities(trim($username))); },
	"/([\\s\\S]*?)~~~~(?:\\r\\n|\\r|\\n)\\s*(.+?)\\s*(?:\\r\\n|\\r|\\n)([\\s\\S]*?)(?:\\r\\n|\\r|\\n)~~~~([\\s\\S]*?)/" => function($_, $prefix, $quotee, $content, $postfix)
	{ return sprintf('%s<blockquote><span class="User Name">%s</span><br />%s</blockquote>%s', $GLOBALS["ycode_global_string_scanner"]->replace($prefix), utf8_html_entities($quotee), $GLOBALS["ycode_global_string_scanner"]->replace($content), $GLOBALS["ycode_global_string_scanner"]->replace($postfix)); },
	"/</" => "&lt;",
	"/>/" => "&gt;",
	"/&/" => "&amp;",
	"/([\\s\\S]*?)\\[rauw\\]\\s*([\\s\\S]*?)\\s*\\[\\/rauw\\]([\\s\\S]*?)" => function($_, $prefix, $content, $postfix)
	{ return sprintf('%s<div class="slideContainer"><a href="#" class="ShowHidden">[rauwkost]</a><div class="SlideText">%s<span class="HideHiddenLink"><a href="#" class="HideHidden">[rauwkost]</a></span></div></div>%s', $GLOBALS["ycode_global_string_scanner"]->replace($prefix), $GLOBALS["ycode_global_string_scanner"]->replace($content), $GLOBALS["ycode_global_string_scanner"]->replace($postfix)); },
	"/(?:\\r\\n|\\r|\\n)/" => "<br />",
	"/(https?:\\/\\/|www\\d{0,3}\\.)([-\\w\\.]+)+(:\\d+)?(\\/([\\w\\/_\\.]*(\\?\\S+)?)?)?/" => function($url)
	{
		if(substr($url, 0, 3) == "www") $url = "http://".$url;
		$url_data = parse_url($url);
		if((isset($url_data["query"]) && preg_match("/youtube\.[a-z]/", $url_data["host"]) && preg_match("/v=([a-z0-9_-]{11})/i", $url_data["query"], $match)))
			return sprintf('<embed src="http://www.youtube.com/v/%s&fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="583" height="354" class="auto-embedded" />', utf8_html_entities($match[1]));
		if(preg_match("/\\.(?:jpe?g|gif|png)/i", $url_data["path"]))
			return sprintf('<img src="%s" class="auto-embedded" />', utf8_html_entities($url));
		else
		{
			$text = sprintf("[%s]", $url_data["host"]);
			return sprintf('<a href="%s" rel="nofollow" class="auto-embedded">%s</a>', utf8_html_entities($url), utf8_html_entities($text));
		}
	}
) as $pattern => $callback)
{
	$GLOBALS["ycode_global_string_scanner"]->register_preg_replacement($pattern, $callback);
	$GLOBALS["ycode_nested_string_scanner"]->register_preg_replacement($pattern, $callback);
}

$GLOBALS["ycode_global_string_scanner"]->register_preg_replacement("/(?:\\^(\\d)|\\A)([\\s\\S]+?)(?=\\Z|\\^\\d)/", function($_, $color, $content)
{ return sprintf('<span class="color color%d">%s</span>', intval($color), $GLOBALS["ycode_nested_string_scanner"]->replace($content)); });

function parse_ycode_formatted_string_to_html( $string )
{ return $GLOBALS["ycode_global_string_scanner"]->replace($string); }

function y2html( $string )
{ return parse_ycode_formatted_string_to_html($string); }

?>