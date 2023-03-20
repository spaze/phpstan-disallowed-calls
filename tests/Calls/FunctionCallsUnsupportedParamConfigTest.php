<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\IdentifierFormatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedRuleErrors;

class FunctionCallsUnsupportedParamConfigTest extends PHPStanTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	public function testUnsupportedArrayInParamConfig(): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessage('{foo(),bar()}: Parameter #2 $definitelyNotScalar has an unsupported type array specified in configuration');
		new FunctionCalls(
			new DisallowedRuleErrors(new AllowedPath(new FileHelper(__DIR__))),
			new DisallowedCallFactory(new IdentifierFormatter()),
			[
				[
					'function' => [
						'foo()',
						'bar()',
					],
					'disallowParams' => [
						1 => [
							'position' => 1,
							'name' => 'key',
							'value' => 'scalar',
						],
						2 => [
							'position' => 2,
							'name' => 'definitelyNotScalar',
							'value' => [
								'key' => 'unsupported',
							],
						],
					],
				],
			]
		);
	}

}
