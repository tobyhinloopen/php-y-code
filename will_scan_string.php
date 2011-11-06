<?php

require_once "regexp_traits.php";
require_once "util.php";

class WillScanString
{
	private $replacements = array();
	private $replacement_pattern = NULL;

	public function register_preg_replacement_callback( $pattern, $callback )
	{
		$last_replacement = array_peek($this->replacements);
		if($last_replacement)
		{
			$previous_index = $last_replacement[2];
			$pattern_capture_group_count = count(get_capture_groups($pattern));
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
		// very, very dirty hack because PHP sucks.
		$GLOBALS["__will_scan_string_instance"] = $this;
		$result = preg_replace_callback($this->get_replacement_pattern(), function($match)
		{
			global $__will_scan_string_instance;
			list($match, $replacement) = $__will_scan_string_instance->get_match_and_replacement($match);
			return $__will_scan_string_instance->execute_replacement_with_match( $replacement, $match );
		}, $string);
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
		return call_user_func_array( $replacement, $match );
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
		$GLOBALS["__will_scan_string_additional_offset"] = 1;
		$sub_patterns = array();
		foreach($this->replacements as $r)
		{
			$p = $r[0];
			$cpsc = count(get_capture_groups($p));
			$p = preg_replace("/(?:\\A\\/|\\/[a-z]*\\Z)/", "", $p);
			$p = preg_replace_callback("/(?<!\\\\\\\\)(?<=\\\\)(\\d+)/", function($m)
			{
				global $__will_scan_string_additional_offset;
				return (string)(intval($m[1]) + $__will_scan_string_additional_offset);
			}, $p);
			$__will_scan_string_additional_offset += 1 + $cpsc;
			array_push($sub_patterns, "(".$p.")");
		}
		return "/(?:".implode("|", $sub_patterns).")/";
	}
}

?>