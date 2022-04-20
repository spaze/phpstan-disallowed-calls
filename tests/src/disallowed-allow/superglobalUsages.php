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


function useNonGlobalVariable()
{
	$randomVar = 'foo';
	echo $randomVar;
}
