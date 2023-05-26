<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

class NewCallsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$normalizer = new Normalizer();
		$formatter = new Formatter($normalizer);
		$allowed = new Allowed($formatter, $normalizer, new AllowedPath(new FileHelper(__DIR__)));
		return new NewCalls(
			new DisallowedCallsRuleErrors($allowed, new Identifier()),
			new DisallowedCallFactory($formatter, $normalizer, $allowed),
			[
				[
					'method' => '\Constructor\ClassWithConstructor::__construct()',
					'message' => 'class ClassWithConstructor should not be created',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Constructor\ClassWithoutConstructor::__construct()',
					'message' => 'class ClassWithoutConstructor should not be created',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Inheritance\Base::__construct()',
					'message' => 'all your base are belong to us',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'function' => 'DateTime::__construct()',
					'message' => 'no future',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowExceptCaseInsensitiveParams' => [
						1 => 'tomorrow',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/methodCalls.php'], [
			[
				'Calling Inheritance\Base::__construct() (as Inheritance\Sub::__construct()) is forbidden, all your base are belong to us',
				19,
			],
			[
				'Calling Constructor\ClassWithConstructor::__construct() is forbidden, class ClassWithConstructor should not be created',
				32,
			],
			[
				'Calling Constructor\ClassWithoutConstructor::__construct() is forbidden, class ClassWithoutConstructor should not be created',
				34,
			],
			[
				'Calling Constructor\ClassWithoutConstructor::__construct() is forbidden, class ClassWithoutConstructor should not be created',
				36,
			],
			[
				'Calling DateTime::__construct() is forbidden, no future',
				57,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/methodCalls.php'], []);
	}

}
