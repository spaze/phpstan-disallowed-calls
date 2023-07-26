<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedConstantFactory
{

	/** @var Normalizer */
	private $normalizer;


	public function __construct(Normalizer $normalizer)
	{
		$this->normalizer = $normalizer;
	}


	/**
	 * @param array<array{class?:string, constant?:string, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, errorIdentifier?:string, errorTip?:string}> $config
	 * @return list<DisallowedConstant>
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
					$this->normalizer->normalizeNamespace($class ? "{$class}::{$constant}" : $constant),
					$disallowed['message'] ?? null,
					$disallowed['allowIn'] ?? [],
					$disallowed['allowExceptIn'] ?? $disallowed['disallowIn'] ?? [],
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? null
				);
				$disallowedConstants[$disallowedConstant->getConstant()] = $disallowedConstant;
			}
		}
		return array_values($disallowedConstants);
	}

}
