<?php
declare(strict_types = 1);

// phpcs:ignore Squiz.WhiteSpace.FunctionSpacing.Before
function useSuperglobals()
{
	// allowed by path
	echo $GLOBALS['test'];
	echo $_GET['field'];

	// Assigning the global to another variable should also cause an error, but it's allowed here by path
	$fields = $_GET;
}


function useVariableNotInScope()
{
	// should not be allowed, since it's not defined in this scope
	echo $TEST_GLOBAL_VARIABLE;
}


function useVariableInScope()
{
	$TEST_GLOBAL_VARIABLE = '1234';

	// should be allowed, since it's defined in the current scope
	echo $TEST_GLOBAL_VARIABLE;
}
