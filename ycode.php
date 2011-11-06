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
	{ return sprintf("<strong>%s</strong>", utf8_html_entities($content)); },
	"/(?<=\\A|\\r\\n|\\r|\\n)\\/\\/\\s*(.+)/" => function($_, $username)
	{ return sprintf('<a href="user_by_username.php?username=%s" class="auto-embedded">%s</a>', utf8_html_entities(rawurlencode(trim($username))), utf8_html_entities(trim($username))); },
	"/~~~~(?:\\r\\n|\\r|\\n)(.+?)(?:\\r\\n|\\r|\\n)([\\s\\S]*?)(?:\\r\\n|\\r|\\n)~~~~/" => function($_, $quotee, $content)
	{ global $ycode_global_string_scanner;
	  return sprintf('<blockquote><span class="User Name">%s</span>%s</blockquote>', utf8_html_entities($quotee), $ycode_global_string_scanner->replace($content)); },
	"/</" => "&lt;",
	"/>/" => "&gt;",
	"/&/" => "&amp;",
	"/(?:\\r\\n|\\r|\\n)/" => "<br />",
	"/\\b((?:[a-z][\\w-]+:(?:\\/{1,3}|[a-z0-9%])|www\\d{0,3}[.]|[a-z0-9.\\-]+[.][a-z]{2,4}\\/)(?:[^\\s()<>]+|\(([^\\s()<>]+|(\\([^\\s()<>]+\\)))*\\))+(?:\\(([^\\s()<>]+|(\\([^\\s()<>]+\\)))*\\)|[^\\s`!()\\[\\]{};:'\".,<>?«»“”‘’]))/" => function($_, $url)
	{
		if(substr($url, 0, 3) == "www") $url = "http://".$url;
		$url_data = parse_url($url);
		if((preg_match("/youtube\.[a-z]/", $url_data["host"]) && preg_match("/v=([a-z0-9_-]{11})/i", $url_data["query"], $match))
		|| ($url_data["host"] == "youtu.be" || preg_match("/\\/([a-z0-9_-]{11})/i", $url_data["path"], $match)))
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
	$ycode_global_string_scanner->register_preg_replacement($pattern, $callback);
	$ycode_nested_string_scanner->register_preg_replacement($pattern, $callback);
}

function parse_ycode_formatted_string_to_html( $string )
{
	global $ycode_global_string_scanner;
	return $ycode_global_string_scanner->replace($string);
}

?>