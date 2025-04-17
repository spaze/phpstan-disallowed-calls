<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Attributes\CallsWithAttributes;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\Container;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Node\InFunctionNode;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Reproducer\AsCommandHandler;
use Reproducer\EventRecorder;
use Spaze\PHPStan\Rules\Disallowed\Allowed\GetAttributesWhenInSignature;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\HelperRules\SetCurrentClassMethodNameHelperRule;
use Spaze\PHPStan\Rules\Disallowed\HelperRules\SetCurrentFunctionNameHelperRule;
use Spaze\PHPStan\Rules\Disallowed\HelperRules\UnsetCurrentClassMethodNameHelperRule;
use Spaze\PHPStan\Rules\Disallowed\HelperRules\UnsetCurrentFunctionNameHelperRule;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

class ReproducerTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new class (
			self::getContainer(),
			[
				[
					'class' => EventRecorder::class,
					'allowInMethodsWithAttributes' => [AsCommandHandler::class],
					'allowInUse' => true,
				],
			]
		) extends NamespaceUsages {

			private Container $container;


			public function __construct(
				Container $container,
				array $forbiddenNamespaces
			) {
				parent::__construct(
					$container->getByType(DisallowedNamespaceRuleErrors::class),
					$container->getByType(DisallowedNamespaceFactory::class),
					$container->getByType(NamespaceUsageFactory::class),
					$forbiddenNamespaces,
				);
				$this->container = $container;
			}


			public function processNode(Node $node, Scope $scope): array
			{
				if ($node instanceof ClassMethod) {
					$this->container->getByType(SetCurrentClassMethodNameHelperRule::class)->processNode($node, $scope);
				} elseif ($node instanceof InClassMethodNode) {
					$this->container->getByType(UnsetCurrentClassMethodNameHelperRule::class)->processNode($node, $scope);
				} elseif ($node instanceof Function_) {
					$this->container->getByType(SetCurrentFunctionNameHelperRule::class)->processNode($node, $scope);
				} elseif ($node instanceof InFunctionNode) {
					$this->container->getByType(UnsetCurrentFunctionNameHelperRule::class)->processNode($node, $scope);
				}

				return parent::processNode($node, $scope);
			}

		};
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/ReproducerClass.php'], [
			[
				'Class Reproducer\EventRecorder is forbidden.',
				15,
			],
			[
				'Class Reproducer\EventRecorder is forbidden.',
				25,
			]
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
