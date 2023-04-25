<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Normalizer;

class Normalizer
{

	public function normalizeCall(string $call): string
	{
		$call = substr($call, -2) === '()' ? substr($call, 0, -2) : $call;
		return $this->normalizeNamespace($call);
	}


	public function normalizeNamespace(string $namespace): string
	{
		return ltrim($namespace, '\\');
	}

}
