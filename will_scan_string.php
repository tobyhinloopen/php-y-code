<?php

require_once "regexp_traits.php";
require_once "util.php";

$__will_scan_string_instances = array();
$__will_scan_string_additional_offsets = array();

class WillScanString
{
	private $replacements = array();
	private $replacement_pattern = NULL;

	public function register_replacement( $pattern, $replacement )
	{
		$pattern = preg_quote($pattern, "/");
		$this->register_preg_replacement( $pattern, $replacement );
	}

	public function register_preg_replacement( $pattern, $callback )
	{
		$last_replacement = array_peek($this->replacements);
		if($last_replacement)
		{
			$previous_index = $last_replacement[2];
			$pattern_capture_group_count = count(get_capture_groups($last_replacement[0]));
			$index = $previous_index + $pattern_capture_group_count + 1;
		}
		else
			$index = 0;
		$replacement = array( $pattern, $callback, $index );
		$this->replacement_pattern = NULL;
		array_push($this->replacements, $replacement);
	}

	public function replace( $string )
	{
		global $__will_scan_string_instances;
		array_push($__will_scan_string_instances, $this);
		$result = preg_replace_callback($this->get_replacement_pattern(), function($match)
		{
			global $__will_scan_string_instances;
			$will_scan_string_instance = array_peek($__will_scan_string_instances);
			list($match, $replacement) = $will_scan_string_instance->get_match_and_replacement($match);
			return $will_scan_string_instance->execute_replacement_with_match( $replacement, $match );
		}, $string);
		array_pop($__will_scan_string_instances);
		return $result;
	}

	public function get_match_and_replacement( $match )
	{
		array_shift($match);
		$index = -1;
		foreach($match as $i => $m)
		{
			if($m != "")
			{
				$index = $i;
				break;
			}
		}
		$replacement = $this->find_replacement_by_index($index);
		$match = array_slice($match, $index, count(get_capture_groups($replacement[0]))+1);
		return array($match, $replacement[1]);
	}

	public function execute_replacement_with_match( $replacement, $match )
	{
		if(is_callable($replacement))
			return call_user_func_array( $replacement, $match );
		else
			return $replacement;
	}

	public function find_replacement_by_index( $index )
	{
		foreach($this->replacements as $r)
			if($r[2] == $index)
				return $r;
		return null;
	}

	public function get_replacement_pattern()
	{
		if($this->replacement_pattern === NULL)
			$this->replacement_pattern = $this->reconstruct_replacement_pattern();
		return $this->replacement_pattern;
	}

	public function reconstruct_replacement_pattern()
	{
		global $__will_scan_string_additional_offsets;
		array_push($__will_scan_string_additional_offsets, 1);
		$sub_patterns = array();
		foreach($this->replacements as $r)
		{
			$p = $r[0];
			$cpsc = count(get_capture_groups($p));
			$p = preg_replace("/(?:\\A\\/|\\/[a-z]*\\Z)/", "", $p);
			$p = preg_replace_callback("/(?<!\\\\\\\\)(?<=\\\\)(\\d+)/", function($m)
			{
				global $__will_scan_string_additional_offsets;
				return (string)(intval($m[1]) + array_peek($__will_scan_string_additional_offsets));
			}, $p);
			$__will_scan_string_additional_offsets[count($__will_scan_string_additional_offsets)-1] += 1 + $cpsc;
			array_push($sub_patterns, "(".$p.")");
		}
		array_pop($__will_scan_string_additional_offsets);
		return "/(?:".implode("|", $sub_patterns).")/";
	}
}

?>