<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;

class DisallowedAttributeFactory
{

	/** @var Allowed */
	private $allowed;


	public function __construct(Allowed $allowed)
	{
		$this->allowed = $allowed;
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
					$attribute,
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
