<?php
declare(strict_types = 1);

namespace Types;

use Waldo\Foo\Bar;
use Waldo\Quux\Blade;

class TypedClassConstants
{

	const ?Blade NULLABLE = null;
	const Blade|null UNION = null;
	const (Blade&Bar)|null DNF = null;
	const int SCALAR = 1;

}
