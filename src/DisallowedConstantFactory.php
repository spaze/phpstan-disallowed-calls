<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;

class DisallowedConstantFactory
{

	/**
	 * @param array<array{class?:string, constant?:string, message?:string, allowIn?:string[], errorIdentifier?:string}> $config
	 * @return DisallowedConstant[]
	 * @throws ShouldNotHappenException
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedConstants = [];
		foreach ($config as $disallowed) {
			$constants = $disallowed['constant'] ?? null;
			unset($disallowed['constant']);
			if (!$constants) {
				throw new ShouldNotHappenException("'constant' must be set in configuration items");
			}
			foreach ((array)$constants as $constant) {
				$class = $disallowed['class'] ?? null;
				$disallowedConstant = new DisallowedConstant(
					$class ? "{$class}::{$constant}" : $constant,
					$disallowed['message'] ?? null,
					$disallowed['allowIn'] ?? [],
					$disallowed['errorIdentifier'] ?? ''
				);
				$disallowedConstants[$disallowedConstant->getConstant()] = $disallowedConstant;
			}
		}
		return array_values($disallowedConstants);
	}

}
