<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedAttributeFactory
{

	/**
	 * @param array $config
	 * @phpstan-param DisallowedAttributesConfig $config
	 * @return DisallowedAttribute[]
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
					$disallowed['allowIn'] ?? [],
					$disallowed['allowExceptIn'] ?? $disallowed['disallowIn'] ?? [],
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? null
				);
				$disallowedAttributes[$disallowedAttribute->getAttribute()] = $disallowedAttribute;
			}
		}
		return array_values($disallowedAttributes);
	}

}
