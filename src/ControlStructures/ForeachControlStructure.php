<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PhpParser\Node;
use PhpParser\Node\Stmt\Foreach_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructure;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedControlStructureRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;

/**
 * Reports on using the foreach loop.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<Foreach_>
 */
class ForeachControlStructure implements Rule
{

	private DisallowedControlStructureRuleErrors $disallowedControlStructureRuleErrors;

	/** @var list<DisallowedControlStructure> */
	private array $disallowedControlStructures;


	/**
	 * @param DisallowedControlStructureRuleErrors $disallowedControlStructureRuleErrors
	 * @param list<DisallowedControlStructure> $disallowedControlStructures
	 */
	public function __construct(DisallowedControlStructureRuleErrors $disallowedControlStructureRuleErrors, array $disallowedControlStructures)
	{
		$this->disallowedControlStructureRuleErrors = $disallowedControlStructureRuleErrors;
		$this->disallowedControlStructures = $disallowedControlStructures;
	}


	public function getNodeType(): string
	{
		return Foreach_::class;
	}


	/**
	 * @param Foreach_ $node
	 * @param Scope $scope
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		return $this->disallowedControlStructureRuleErrors->get($node, $scope, 'foreach', $this->disallowedControlStructures, ErrorIdentifiers::DISALLOWED_FOREACH);
	}

}
