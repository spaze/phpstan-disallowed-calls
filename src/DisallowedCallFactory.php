<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedCallFactory
{

	/** @var Formatter */
	private $formatter;

	/** @var Normalizer */
	private $normalizer;

	/** @var Allowed */
	private $allowed;


	public function __construct(Formatter $formatter, Normalizer $normalizer, Allowed $allowed)
	{
		$this->formatter = $formatter;
		$this->normalizer = $normalizer;
		$this->allowed = $allowed;
	}


	/**
	 * @param array $config
	 * @phpstan-param ForbiddenCallsConfig $config
	 * @noinspection PhpUndefinedClassInspection ForbiddenCallsConfig is a type alias defined in PHPStan config
	 * @return list<DisallowedCall>
	 * @throws ShouldNotHappenException
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedCalls = [];
		foreach ($config as $disallowed) {
			$calls = $disallowed['function'] ?? $disallowed['method'] ?? null;
			unset($disallowed['function'], $disallowed['method']);
			if (!$calls) {
				throw new ShouldNotHappenException("Either 'method' or 'function' must be set in configuration items");
			}
			$excludes = [];
			foreach ((array)($disallowed['exclude'] ?? []) as $exclude) {
				$excludes[] = $this->normalizer->normalizeCall($exclude);
			}
			$calls = array_values((array)$calls);
			try {
				foreach ($calls as $call) {
					$disallowedCall = new DisallowedCall(
						$this->normalizer->normalizeCall($call),
						$excludes,
						(array)($disallowed['definedIn'] ?? []),
						$disallowed['message'] ?? null,
						$this->allowed->getConfig($disallowed),
						$disallowed['errorIdentifier'] ?? null,
						$disallowed['errorTip'] ?? null
					);
					$disallowedCalls[$disallowedCall->getKey()] = $disallowedCall;
				}
			} catch (UnsupportedParamTypeInConfigException $e) {
				throw new ShouldNotHappenException(sprintf('%s: %s', $this->formatter->formatIdentifier($calls), $e->getMessage()));
			}
		}
		return array_values($disallowedCalls);
	}

}
