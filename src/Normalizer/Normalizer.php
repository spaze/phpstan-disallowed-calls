<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Normalizer;

class Normalizer
{

	public function normalizeCall(string $call): string
	{
		$call = $this->removeParentheses($call);
		return $this->normalizeNamespace($call);
	}


	public function normalizeNamespace(string $namespace): string
	{
		return ltrim($namespace, '\\');
	}


	public function normalizeAttribute(string $attribute): string
	{
		$attribute = ltrim($attribute, '#[');
		$attribute = rtrim($attribute, ']');
		$attribute = $this->removeParentheses($attribute);
		return $this->normalizeNamespace($attribute);
	}


	private function removeParentheses(string $element): string
	{
		return substr($element, -2) === '()' ? substr($element, 0, -2) : $element;
	}

}
