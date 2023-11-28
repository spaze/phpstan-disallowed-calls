<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node\Attribute;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttribute;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;

class DisallowedAttributeRuleErrors
{

	/** @var Allowed */
	private $allowed;

	/** @var Identifier */
	private $identifier;

	/** @var Formatter */
	private $formatter;


	public function __construct(Allowed $allowed, Identifier $identifier, Formatter $formatter)
	{
		$this->allowed = $allowed;
		$this->identifier = $identifier;
		$this->formatter = $formatter;
	}


	/**
	 * @param Attribute $attribute
	 * @param Scope $scope
	 * @param list<DisallowedAttribute> $disallowedAttributes
	 * @return list<RuleError>
	 */
	public function get(Attribute $attribute, Scope $scope, array $disallowedAttributes): array
	{
		foreach ($disallowedAttributes as $disallowedAttribute) {
			$attributeName = $attribute->name->toString();
			if (!$this->identifier->matches($disallowedAttribute->getAttribute(), $attributeName, $disallowedAttribute->getExcludes())) {
				continue;
			}
			if ($this->allowed->isAllowed($scope, $attribute->args, $disallowedAttribute)) {
				continue;
			}

			$errorBuilder = RuleErrorBuilder::message(sprintf(
				'Attribute %s is forbidden%s%s',
				$attributeName,
				$this->formatter->formatDisallowedMessage($disallowedAttribute->getMessage()),
				$disallowedAttribute->getAttribute() !== $attributeName ? " [{$attributeName} matches {$disallowedAttribute->getAttribute()}]" : ''
			));
			if ($disallowedAttribute->getErrorIdentifier()) {
				$errorBuilder->identifier($disallowedAttribute->getErrorIdentifier());
			}
			if ($disallowedAttribute->getErrorTip()) {
				$errorBuilder->tip($disallowedAttribute->getErrorTip());
			}
			return [
				$errorBuilder->build(),
			];
		}

		return [];
	}

}
