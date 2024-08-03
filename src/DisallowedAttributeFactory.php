<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedAttributeFactory
{

	public function __construct(
		private readonly Allowed $allowed,
		private readonly Normalizer $normalizer,
	) {
	}


	/**
	 * @phpstan-param DisallowedAttributesConfig $config
	 * @return list<DisallowedAttribute>
	 * @throws UnsupportedParamTypeInConfigException
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedAttributes = [];
		foreach ($config as $disallowed) {
			$attributes = $disallowed['attribute'];
			$excludes = [];
			foreach ((array)($disallowed['exclude'] ?? []) as $exclude) {
				$excludes[] = $this->normalizer->normalizeAttribute($exclude);
			}
			foreach ((array)$attributes as $attribute) {
				$disallowedAttribute = new DisallowedAttribute(
					$this->normalizer->normalizeAttribute($attribute),
					$excludes,
					$disallowed['message'] ?? null,
					$this->allowed->getConfig($disallowed),
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? null,
				);
				$disallowedAttributes[$disallowedAttribute->getAttribute()] = $disallowedAttribute;
			}
		}
		return array_values($disallowedAttributes);
	}

}
