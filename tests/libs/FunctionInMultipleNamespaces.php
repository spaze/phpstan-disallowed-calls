<?php
declare(strict_types=1);

namespace {

	function __(): string
	{
	}

}

namespace MyNamespace {

	use function __ as alias;

	function __(): string
	{
		return alias();
	}

	function someOtherFn(): string
	{
		return __();  // The __ used here is MyNamespace\__
	}

}
