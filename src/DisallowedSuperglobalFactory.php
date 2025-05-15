<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfigFactory;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;

class DisallowedSuperglobalFactory
{

	/**
	 * @see https://www.php.net/variables.superglobals
	 */
	private const SUPERGLOBALS = [
		'$GLOBALS',
		'$_SERVER',
		'$_GET',
		'$_POST',
		'$_FILES',
		'$_COOKIE',
		'$_SESSION',
		'$_REQUEST',
		'$_ENV',
	];

	private Formatter $formatter;

	private AllowedConfigFactory $allowedConfigFactory;


	public function __construct(Formatter $formatter, AllowedConfigFactory $allowedConfigFactory)
	{
		$this->formatter = $formatter;
		$this->allowedConfigFactory = $allowedConfigFactory;
	}


	/**
	 * @param array<array{superglobal?:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, errorIdentifier?:string, errorTip?:string}> $config
	 * @return list<DisallowedVariable>
	 * @throws ShouldNotHappenException
	 */
	public function getDisallowedVariables(array $config): array
	{
		$disallowedSuperglobals = [];
		foreach ($config as $disallowed) {
			$superglobals = $disallowed['superglobal'] ?? null;
			unset($disallowed['superglobal']);
			if (!$superglobals) {
				throw new ShouldNotHappenException("'superglobal' must be set in configuration items");
			}
			$superglobals = (array)$superglobals;
			try {
				foreach ($superglobals as $superglobal) {
					if (!in_array($superglobal, self::SUPERGLOBALS, true)) {
						throw new ShouldNotHappenException("{$superglobal} is not a superglobal variable");
					}
					$disallowedSuperglobal = new DisallowedVariable(
						$superglobal,
						$disallowed['message'] ?? null,
						$this->allowedConfigFactory->getConfig($disallowed),
						$disallowed['errorIdentifier'] ?? null,
						$disallowed['errorTip'] ?? null
					);
					$disallowedSuperglobals[$disallowedSuperglobal->getVariable()] = $disallowedSuperglobal;
				}
			} catch (UnsupportedParamTypeInConfigException $e) {
				throw new ShouldNotHappenException(sprintf('%s: %s', $this->formatter->formatIdentifier($superglobals), $e->getMessage()));
			}
		}
		return array_values($disallowedSuperglobals);
	}

}
