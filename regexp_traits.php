<?php

function get_capture_groups( $regular_expression )
{
	$capture_group_pattern = "/(?<!\\\\)\\((?:\\?(?:<([a-z]+)\\>|'([a-z]+)')|(?!\\?))/i";
	$matches = array();
	preg_match_all($capture_group_pattern, $regular_expression, $matches, PREG_SET_ORDER);
	$groups = array();
	$c = 1;
	foreach($matches as $match)
	{
		if($match[1] != "")
			$name = $match[1];
		elseif($match[2] != "")
			$name = $match[2];
		else
			$name = $c++;
		array_push($groups, $name);
	}
	return $groups;
}

?>