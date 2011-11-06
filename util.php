<?php

function array_peek( $array )
{ return $array[count($array)-1]; }

function utf8_html_entities( $string )
{ return htmlentities($string, ENT_COMPAT, "UTF-8"); }

?>