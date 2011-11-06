<?php

require_once "test_helpers.php";
require_once "regexp_traits.php";

frseql("should match regular capture groups", function()
{ return get_capture_groups("/(a)/");
}, array(1));

frseql("should not match escaped capture groups", function()
{ return get_capture_groups("/\\(\\)/");
}, array());

frseql("should match named capture groups with less-than & greater-than characters", function()
{ return get_capture_groups("/(?<a>a)/");
}, array("a"));

frseql("should match named capture groups with single quotes", function()
{ return get_capture_groups("/(?'a'a)/");
}, array("a"));

frseql("should match nested capture groups", function()
{ return get_capture_groups("/(a(b))/");
}, array(1, 2));

frseql("should ignore non-capture groups", function()
{ return get_capture_groups("/(?:a)/");
}, array());

frseql("should ignore look-aheads and look-behinds", function()
{ return get_capture_groups("/(?=a)(?!a)(?<=a)(?<!a)/");
}, array());

$bbcode_pattern = "/\\[(\\/?)([a-z0-9_-]*)(\\s*=?(?:(?:\\s*(?:(?:[a-z0-9_-]+)|(?<=\\=))\\s*[:=]\\s*)?(?:\"[^\"\\\\]*(?:\\\\[\\s\\S][^\"\\\\]*)*\"|'[^'\\\\]*(?:\\\\[\\s\\S][^'\\\\]*)*'|[^\\]\\s,]+|(?<=,)(?=\\s*,))\\s*,?\\s*)*)\\]/i";
frseql("should match the capture groups in my bbcode parser's regular expressions", function()
{ global $bbcode_pattern;
  return get_capture_groups($bbcode_pattern);
}, array(1, 2, 3));

frseql("should handle multiline regular expressions", function()
{ return get_capture_groups("/(?:\\r\\n|\\r|\\n)/");
}, array())

?>