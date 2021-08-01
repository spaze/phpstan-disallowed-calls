<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;

class DisallowedConstantFactory
{

	/**
	 * @param array<array{class?:string, constant?:string, message?:string, allowIn?:string[]}> $config
	 * @return DisallowedConstant[]
	 * @throws ShouldNotHappenException
	 */
	public function createFromConfig(array $config): array
	{
		$constants = [];
		foreach ($config as $disallowedConstant) {
			$constant = $disallowedConstant['constant'] ?? null;
			if (!$constant) {
				throw new ShouldNotHappenException("'constant' must be set in configuration items");
			}
			$class = $disallowedConstant['class'] ?? null;
			$disallowedConstant = new DisallowedConstant(
				$class ? "{$class}::{$constant}" : $constant,
				$disallowedConstant['message'] ?? null,
				$disallowedConstant['allowIn'] ?? []
			);
			$constants[$disallowedConstant->getConstant()] = $disallowedConstant;
		}
		return array_values($constants);
	}

}
