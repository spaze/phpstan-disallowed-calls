<?php
declare(strict_types = 1);

namespace Types;

use Waldo\Foo\Bar;
use Waldo\Quux\Blade;

class DnfTypesEverywhere
{

	public (Blade&Bar)|null $dnf;


	public function __construct(
		private readonly (Blade&Bar)|null $privateDnf,
	) {
	}


	public function dnf(
		(Blade&Bar)|null $dnf,
	): (Blade&Bar)|null {
		return null;
	}

}
