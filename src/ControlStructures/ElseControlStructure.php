<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PhpParser\Node;
use PhpParser\Node\Stmt\Else_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructure;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedControlStructureRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;

/**
 * Reports on using the else control structure.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<Else_>
 */
class ElseControlStructure implements Rule
{

	/** @var DisallowedControlStructureRuleErrors */
	private $disallowedControlStructureRuleErrors;

	/** @var list<DisallowedControlStructure> */
	private $disallowedControlStructures;


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
		return Else_::class;
	}


	/**
	 * @param Else_ $node
	 * @param Scope $scope
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		return $this->disallowedControlStructureRuleErrors->get($scope, 'else', $this->disallowedControlStructures, ErrorIdentifiers::DISALLOWED_ELSE);
	}

}
