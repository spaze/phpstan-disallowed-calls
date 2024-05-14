<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PhpParser\Node;
use PhpParser\Node\Expr\Include_;
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
 * @implements Rule<Include_>
 */
class RequireIncludeControlStructure implements Rule
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
		return Include_::class;
	}


	/**
	 * @param Include_ $node
	 * @param Scope $scope
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$type = $identifier = null;
		switch ($node->type) {
			case Include_::TYPE_INCLUDE:
				$type = 'include';
				$identifier = ErrorIdentifiers::DISALLOWED_INCLUDE;
				break;
			case Include_::TYPE_REQUIRE:
				$type = 'require';
				$identifier = ErrorIdentifiers::DISALLOWED_REQUIRE;
				break;
			case Include_::TYPE_INCLUDE_ONCE:
				$type = 'include_once';
				$identifier = ErrorIdentifiers::DISALLOWED_INCLUDE_ONCE;
				break;
			case Include_::TYPE_REQUIRE_ONCE:
				$type = 'require_once';
				$identifier = ErrorIdentifiers::DISALLOWED_REQUIRE_ONCE;
				break;
		}
		if ($type === null) {
			return [];
		}
		return $this->disallowedControlStructureRuleErrors->get($scope, $type, $this->disallowedControlStructures, $identifier);
	}

}
