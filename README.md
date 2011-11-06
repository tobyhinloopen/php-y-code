Y-Code parser
=============
A parser for a customized markup language used on http://y-ch.at/

This PHP-based Y-code to HTML converter is based on a PHP port of my
`will_scan_string` gem.

I'm not a PHP developer. The code therefore contains some nasty hacks.

Installation
------------
Download and copy `regexp_traits.php`, `util.php`, `will_scan_string.php` and
`ycode.php` to any folder you like.

Include `ycode.php` in your PHP script to use the `y2html` function to convert
your Y-code string to HTML.

Example, assuming you stored the PHP files in the folder `ycode/`:
```
require_once("ycode/ycode.php");

print y2html("^1Hello ^4world!");
```

Also, for coloring to work, you need to add something like this to your CSS:
```
.color, .color0, .color8, .color9 { color: inherit; }
.color0 { color: inherit; }
.color1 { color: red; }
.color2 { color: green; }
.color3 { color: #ffcc00; }
.color4 { color: blue; }
.color5 { color: #00ffff; }
.color6 { color: purple; }
.color7 { color: #ffffff; }
```

Y-Code Syntax
-------------
- `http://www.google.com/` Will be converted to a clickable link with text
  `[www.google.com]`.
- `^1red^0` Will be converted to a red-colored word `red`. `^0` 'till `^9`
  switches colors. You can modify the CSS class `color0` 'till `color9` to
  change the colors.
- `http://www.youtube.com/watch?v=FErzTCzR5N4` Will be converted with an
  embedded youtube video.
- Any URL ending with `.jpg`, `.png` or `.gif` will be converted to an
  embedded image.
- `[rauw]text[/rauw]` Will be converted to a bunch of DIVs and SPANs. Is used
  like a spoiler-tag on y-ch.at, but this package lacks the required Javascript
  code to make it work.
- `~~~~\r\nName:\r\nMessage\r\n~~~~` Will be converted to a quote.
- `_bold_` Will be converted to the word `bold` in bold text.
- `// tobyhinloopen` Will be converted to a link to
  `user_by_username.php?username=tobyhinloopen` with the text `tobyhinloopen`.
- Newlines will be converted to `<br />`'s.
- HTML entities will be escaped.