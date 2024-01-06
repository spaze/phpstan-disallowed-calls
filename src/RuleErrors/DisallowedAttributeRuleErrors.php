<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node\Attribute;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttribute;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;

class DisallowedAttributeRuleErrors
{

	public function __construct(
		private readonly Allowed $allowed,
		private readonly Identifier $identifier,
		private readonly Formatter $formatter,
	) {
	}


	/**
	 * @param list<DisallowedAttribute> $disallowedAttributes
	 * @return list<IdentifierRuleError>
	 * @throws ShouldNotHappenException
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
				$disallowedAttribute->getAttribute() !== $attributeName ? " [{$attributeName} matches {$disallowedAttribute->getAttribute()}]" : '',
			));
			$errorBuilder->identifier($disallowedAttribute->getErrorIdentifier() ?? ErrorIdentifiers::DISALLOWED_ATTRIBUTE);
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
