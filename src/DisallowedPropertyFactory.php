<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfigFactory;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\InvalidConfigException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedPropertyFactory
{

	private AllowedConfigFactory $allowedConfigFactory;

	private Normalizer $normalizer;

	private Formatter $formatter;


	public function __construct(AllowedConfigFactory $allowedConfigFactory, Normalizer $normalizer, Formatter $formatter)
	{
		$this->allowedConfigFactory = $allowedConfigFactory;
		$this->normalizer = $normalizer;
		$this->formatter = $formatter;
	}


	/**
	 * @param array<array{property:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, allowInFunctions?:list<string>, allowInMethods?:list<string>, allowExceptInFunctions?:list<string>, allowExceptInMethods?:list<string>, disallowInFunctions?:list<string>, disallowInMethods?:list<string>, allowInInstanceOf?:list<string>, allowExceptInInstanceOf?:list<string>, disallowInInstanceOf?:list<string>, allowInClassWithAttributes?:list<string>, allowExceptInClassWithAttributes?:list<string>, disallowInClassWithAttributes?:list<string>, allowInFunctionsWithAttributes?:list<string>, allowInMethodsWithAttributes?:list<string>, allowExceptInFunctionsWithAttributes?:list<string>, allowExceptInMethodsWithAttributes?:list<string>, disallowInFunctionsWithAttributes?:list<string>, disallowInMethodsWithAttributes?:list<string>, allowInClassWithMethodAttributes?:list<string>, allowExceptInClassWithMethodAttributes?:list<string>, disallowInClassWithMethodAttributes?:list<string>, errorIdentifier?:string, errorTip?:string|list<string>}> $config
	 * @return list<DisallowedProperty>
	 * @throws ShouldNotHappenException
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedProperties = [];
		foreach ($config as $disallowed) {
			$properties = (array)$disallowed['property'];
			try {
				foreach ($properties as $property) {
					$disallowedProperty = new DisallowedProperty(
						$this->normalizer->normalizeProperty($property),
						$disallowed['message'] ?? null,
						$this->allowedConfigFactory->getConfig($disallowed),
						$disallowed['errorIdentifier'] ?? null,
						$disallowed['errorTip'] ?? []
					);
					$disallowedProperties[$disallowedProperty->getProperty()] = $disallowedProperty;
				}
			} catch (InvalidConfigException $e) {
				throw new ShouldNotHappenException(sprintf('%s: %s', $this->formatter->formatIdentifier($properties), $e->getMessage()));
			}
		}
		return array_values($disallowedProperties);
	}

}
