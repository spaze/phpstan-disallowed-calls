<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Normalizer;

class Normalizer
{

	public function normalizeCall(string $call): string
	{
		$call = substr($call, -2) === '()' ? substr($call, 0, -2) : $call;
		return ltrim($call, '\\');
	}

}
