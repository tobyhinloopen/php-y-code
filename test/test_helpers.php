<?php

// Ensures the browser renders the result as plain text, for if the tests are
// ran from the browser.
header("Content-Type: text/plain; charset=utf-8");

set_include_path(get_include_path().PATH_SEPARATOR."./..");

print "Running tests using PHP ".phpversion()."\r\n";

$timer_stack = array();

function push_timer($name = NULL)
{
	global $timer_stack;
	array_push($timer_stack, array(microtime(true), $name));
}

function pop_timer()
{
	global $timer_stack;
	$timer = array_pop($timer_stack);
	$ms_time_difference = ( microtime(true) - $timer[0] ) * 1000;
	$timer_name = $timer[1];
	printf("Finished %s in %.2f milliseconds\r\n", $timer_name, $ms_time_difference);
}

push_timer("all tests");

function tests_finished()
{ pop_timer(); }

register_shutdown_function("tests_finished");

function should_eql($name, $result, $expected)
{
	if($expected !== $result)
	{
		printf("\r\nTest '%s' failed:\r\n", $name);
		print("  Expected: ");
		var_dump($expected);
		print("       Got: ");
		var_dump($result);
		print("\r\n");
	}
}

function function_result_should_eql($test_name, $function_name, $expected_result)
{
	push_timer($test_name);
	$result = call_user_func($function_name);
	pop_timer();
	should_eql($test_name, $result, $expected_result);
}

function_result_should_eql("testing function_result_should_eql", function()
{
	return "foo";
}, "foo");

?>