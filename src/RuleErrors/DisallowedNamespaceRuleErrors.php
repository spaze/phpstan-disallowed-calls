<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespace;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;
use Spaze\PHPStan\Rules\Disallowed\Usages\NamespaceUsage;

class DisallowedNamespaceRuleErrors
{

	private Allowed $allowed;

	private Identifier $identifier;

	private Formatter $formatter;


	public function __construct(Allowed $allowed, Identifier $identifier, Formatter $formatter)
	{
		$this->allowed = $allowed;
		$this->identifier = $identifier;
		$this->formatter = $formatter;
	}


	/**
	 * @param Node $node
	 * @param NamespaceUsage $namespaceUsage
	 * @param string $description
	 * @param Scope $scope
	 * @param list<DisallowedNamespace> $disallowedNamespaces
	 * @param string $identifier
	 * @return list<IdentifierRuleError>
	 */
	public function getDisallowedMessage(Node $node, NamespaceUsage $namespaceUsage, string $description, Scope $scope, array $disallowedNamespaces, string $identifier): array
	{
		foreach ($disallowedNamespaces as $disallowedNamespace) {
			if (
				!$this->identifier->matches($disallowedNamespace->getNamespace(), $namespaceUsage->getNamespace(), $disallowedNamespace->getExcludes(), $disallowedNamespace->getExcludeWithAttributes())
				|| $this->allowed->isAllowed($node, $scope, null, $disallowedNamespace)
				|| ($disallowedNamespace->isAllowInUse() && $namespaceUsage->isUseItem())
			) {
				continue;
			}

			$errorBuilder = RuleErrorBuilder::message(sprintf(
				'%s %s is forbidden%s%s',
				$description,
				$namespaceUsage->getNamespace(),
				$this->formatter->formatDisallowedMessage($disallowedNamespace->getMessage()),
				$disallowedNamespace->getNamespace() !== $namespaceUsage->getNamespace() ? " [{$namespaceUsage->getNamespace()} matches {$disallowedNamespace->getNamespace()}]" : ''
			));
			$errorBuilder->identifier($disallowedNamespace->getErrorIdentifier() ?? $identifier);
			if ($disallowedNamespace->getErrorTip()) {
				$errorBuilder->tip($disallowedNamespace->getErrorTip());
			}
			return [
				$errorBuilder->build(),
			];
		}

		return [];
	}

}
