<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Attributes\CallsWithAttributes;
use Constructor\ClassWithConstructor;
use Constructor\ClassWithoutConstructor;
use PhpOption\None;
use PhpOption\Some;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\Container;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Node\InFunctionNode;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed\GetAttributesWhenInSignature;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

/**
 * @extends RuleTestCase<NamespaceUsages>
 */
class NamespaceUsagesAllowInClassWithAttributesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new class (
			self::getContainer(),
			[
				[
					'namespace' => 'Waldo\Foo\Bar',
					'allowInClassWithAttributes' => [
						'\Attributes\Attribute2',
					],
				],
				[
					'class' => 'Waldo\Quux\Blade',
					'disallowInClassWithAttributes' => [
						'\Attributes\Attribute3',
					],
					'allowInUse' => true,
				],
				[
					'class' => None::class,
					'allowInMethodsWithAttributes' => [
						'Attribute10',
					],
					'allowInUse' => true,
				],
				[
					'class' => Some::class,
					'allowExceptInFunctionsWithAttributes' => [
						'Attribute11',
					],
					'allowInUse' => true,
				],
				[
					'class' => ClassWithConstructor::class,
					'allowInClassWithMethodAttributes' => [
						'Attribute6',
					],
					'allowInUse' => true,
				],
				[
					'class' => ClassWithoutConstructor::class,
					'allowExceptInClassWithMethodAttributes' => [
						'Attribute6',
					],
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
				$this->emulateHelperRules($node);
				return parent::processNode($node, $scope);
			}


			/**
			 * Emulates processing the SetCurrentClassMethodNameHelperRule and UnsetCurrentClassMethodNameHelperRule rules,
			 * which are not processed in tests.
			 */
			private function emulateHelperRules(Node $node): void
			{
				if ($node instanceof ClassMethod) {
					$this->container->getByType(GetAttributesWhenInSignature::class)->setCurrentClassMethodName(CallsWithAttributes::class, $node->name->toString());
				} elseif ($node instanceof InClassMethodNode) { /** @phpstan-ignore phpstanApi.instanceofAssumption (🤞) */
					$this->container->getByType(GetAttributesWhenInSignature::class)->unsetCurrentClassMethodName();
				} elseif ($node instanceof Function_ && $node->namespacedName !== null) {
					$this->container->getByType(GetAttributesWhenInSignature::class)->setCurrentFunctionName($node->namespacedName->toString());
				} elseif ($node instanceof InFunctionNode) { /** @phpstan-ignore phpstanApi.instanceofAssumption (🤞) */
					$this->container->getByType(GetAttributesWhenInSignature::class)->unsetCurrentFunctionName();
				}
			}

		};
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeClass.php'], [
			[
				'Namespace Waldo\Foo\Bar is forbidden.',
				7,
			],
			[
				'Class Waldo\Foo\Bar is forbidden.',
				36,
			],
			[
				'Class Waldo\Quux\Blade is forbidden.',
				37,
			],
			[
				'Class Waldo\Foo\Bar is forbidden.',
				50,
			],
			[
				'Class Waldo\Foo\Bar is forbidden.',
				63,
			],
			[
				'Class Constructor\ClassWithConstructor is forbidden.',
				80,
			],
			[
				'Class Constructor\ClassWithoutConstructor is forbidden.',
				101,
			],
			[
				'Class PhpOption\None is forbidden.',
				151,
			],
			[
				'Class PhpOption\Some is forbidden.',
				151,
			],
			[
				'Class PhpOption\None is forbidden.',
				151,
			],
			[
				'Class PhpOption\Some is forbidden.',
				151,
			],
			[
				'Class PhpOption\None is forbidden.',
				155,
			],
			[
				'Class PhpOption\Some is forbidden.',
				156,
			],
		]);
		$this->analyse([__DIR__ . '/../src/Functions.php'], [
			[
				'Class PhpOption\None is forbidden.',
				84,
			],
			[
				'Class PhpOption\Some is forbidden.',
				84,
			],
			[
				'Class PhpOption\None is forbidden.',
				84,
			],
			[
				'Class PhpOption\Some is forbidden.',
				84,
			],
			[
				'Class PhpOption\None is forbidden.',
				86,
			],
			[
				'Class PhpOption\Some is forbidden.',
				87,
			],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
