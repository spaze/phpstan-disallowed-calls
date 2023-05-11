<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\File\FilePath;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

class FunctionCallsUnsupportedParamConfigTest extends PHPStanTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	public function testUnsupportedArrayInParamConfig(): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessage('{foo(),bar()}: Parameter #2 $definitelyNotScalar has an unsupported type array specified in configuration');
		$normalizer = new Normalizer();
		$formatter = new Formatter($normalizer);
		$filePath = new FilePath(new FileHelper(__DIR__));
		$allowed = new Allowed($formatter, $normalizer, new AllowedPath($filePath));
		new FunctionCalls(
			new DisallowedCallsRuleErrors($allowed, new Identifier(), $filePath),
			new DisallowedCallFactory($formatter, $normalizer, $allowed),
			$this->createReflectionProvider(),
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
