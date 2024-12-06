<?php
declare(strict_types = 1);

use Fiction\Pulp;

// disallowed method
\Fiction\Pulp\Royale::withCheese();
Pulp\Royale::WithCheese();
Pulp\Royale::withBadCheese();

// disallowed call, params match allowed but path doesn't
Pulp\Royale::withoutCheese(1, 2, 3);
$a = 3;
Pulp\Royale::WithoutCheese(1, 2, $a);

// disallowed call, params don't match allowed
$a = 5;
Pulp\Royale::withoutCheese(1, 2, $a);

// allowed, params match allowed params
Pulp\Royale::withoutCheese(1, 2, 4);

// not a disallowed method
Pulp\Royale::leBigMac();
Pulp\Royale::withMayo();

// disallowed parent method
Inheritance\Sub::woofer();

// disallowed trait method
Traits\TestClass::z();
Traits\AnotherTestClass::zz();

// types that support generics
PhpOption\Option::fromArraysValue([]);
PhpOption\None::create();
PhpOption\Some::create('value');

// disallowed on interface
Interfaces\Implementation::y();
$foo = new class implements Interfaces\BaseInterface {

	public function x(): void
	{
	}


	public static function y(): void
	{
	}

};
$foo::y();

// disallowed parent method
$foo = new class extends Inheritance\Base {};
$foo::woofer();
