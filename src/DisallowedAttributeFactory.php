<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedAttributeFactory
{

	/** @var Allowed */
	private $allowed;

	/** @var Normalizer */
	private $normalizer;


	public function __construct(Allowed $allowed, Normalizer $normalizer)
	{
		$this->allowed = $allowed;
		$this->normalizer = $normalizer;
	}


	/**
	 * @param array $config
	 * @phpstan-param DisallowedAttributesConfig $config
	 * @return DisallowedAttribute[]
	 * @throws UnsupportedParamTypeInConfigException
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedAttributes = [];
		foreach ($config as $disallowed) {
			$attributes = $disallowed['attribute'];
			foreach ((array)$attributes as $attribute) {
				$disallowedAttribute = new DisallowedAttribute(
					$this->normalizer->normalizeNamespace($attribute),
					$disallowed['message'] ?? null,
					$this->allowed->getConfig($disallowed),
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? null
				);
				$disallowedAttributes[$disallowedAttribute->getAttribute()] = $disallowedAttribute;
			}
		}
		return array_values($disallowedAttributes);
	}

}
