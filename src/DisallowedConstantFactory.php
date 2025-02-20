<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfigFactory;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedConstantFactory
{

	private Formatter $formatter;

	private Normalizer $normalizer;

	private AllowedConfigFactory $allowedConfigFactory;


	public function __construct(Formatter $formatter, Normalizer $normalizer, AllowedConfigFactory $allowedConfigFactory)
	{
		$this->formatter = $formatter;
		$this->normalizer = $normalizer;
		$this->allowedConfigFactory = $allowedConfigFactory;
	}


	/**
	 * @param array<array{class?:string, enum?:string, constant?:string|list<string>, case?:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, errorIdentifier?:string, errorTip?:string}> $config
	 * @return list<DisallowedConstant>
	 * @throws ShouldNotHappenException
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedConstants = [];
		foreach ($config as $disallowed) {
			$constants = $disallowed['constant'] ?? $disallowed['case'] ?? null;
			unset($disallowed['constant'], $disallowed['case']);
			if (!$constants) {
				throw new ShouldNotHappenException("'constant', or 'case' for enums, must be set in configuration items");
			}
			$constants = (array)$constants;
			try {
				foreach ($constants as $constant) {
					$class = $disallowed['class'] ?? $disallowed['enum'] ?? null;
					$disallowedConstant = new DisallowedConstant(
						$this->normalizer->normalizeNamespace($class ? "{$class}::{$constant}" : $constant),
						$disallowed['message'] ?? null,
						$this->allowedConfigFactory->getConfig($disallowed),
						$disallowed['errorIdentifier'] ?? null,
						$disallowed['errorTip'] ?? null
					);
					$disallowedConstants[$disallowedConstant->getConstant()] = $disallowedConstant;
				}
			} catch (UnsupportedParamTypeInConfigException $e) {
				throw new ShouldNotHappenException(sprintf('%s: %s', $this->formatter->formatIdentifier($constants), $e->getMessage()));
			}
		}
		return array_values($disallowedConstants);
	}

}
