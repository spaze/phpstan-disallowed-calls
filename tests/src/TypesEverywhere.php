<?php
declare(strict_types = 1);

namespace Types;

use Waldo\Foo\Bar;
use Waldo\Quux\Blade;

class TypesEverywhere
{

	public Blade $blade;
	public ?Blade $maybeBlade;
	public Blade|null $doYouEvenBlade;
	public Blade&Bar $uWotBl8;


	public function __construct(
		private readonly Blade $privateBlade,
		private readonly ?Blade $privateMaybeBlade,
		private readonly Blade|null $privateDoYouEvenBlade,
		private readonly Blade&Bar $privateUWotBl8,
	) {
	}


	public function blade(
		Blade $blade,
	): Blade {
	}


	public function maybeBlade(
		?Blade $blade,
	): ?Blade {
	}


	public function doYouEvenBlade(
		Blade|null $blade,
	): Blade|null {
	}


	public function uWotBl8(
		Blade&Bar $blade,
	): Blade&Bar {
	}

}
