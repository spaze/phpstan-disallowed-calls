<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfigFactory;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedPropertyFactory
{

	private AllowedConfigFactory $allowedConfigFactory;

	private Normalizer $normalizer;


	public function __construct(AllowedConfigFactory $allowedConfigFactory, Normalizer $normalizer)
	{
		$this->allowedConfigFactory = $allowedConfigFactory;
		$this->normalizer = $normalizer;
	}


	/**
	 * @param array<array{property:string|list<string>, message?:string, errorIdentifier?:string, errorTip?:string|list<string>}> $config + AllowDirectivesConfig
	 * @return list<DisallowedProperty>
	 * @throws UnsupportedParamTypeInConfigException
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedProperties = [];
		foreach ($config as $disallowed) {
			$properties = $disallowed['property'];
			foreach ((array)$properties as $property) {
				$disallowedProperty = new DisallowedProperty(
					$this->normalizer->normalizeProperty($property),
					$disallowed['message'] ?? null,
					$this->allowedConfigFactory->getConfig($disallowed),
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? []
				);
				$disallowedProperties[$disallowedProperty->getProperty()] = $disallowedProperty;
			}
		}
		return array_values($disallowedProperties);
	}

}
