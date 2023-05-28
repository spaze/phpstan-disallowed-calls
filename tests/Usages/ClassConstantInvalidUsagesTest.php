<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\File\FilePath;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedConstantRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class ClassConstantInvalidUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$normalizer = new Normalizer();
		return new ClassConstantUsages(
			new DisallowedConstantRuleErrors(new AllowedPath(new FilePath(new FileHelper(__DIR__)))),
			new DisallowedConstantFactory($normalizer),
			new TypeResolver(),
			new Formatter($normalizer),
			[]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/invalid/constantUsages.php'], [
			[
				// expect this error message:
				'Cannot access constant GLITTER on string',
				// on this line:
				6,
			],
			[
				'Cannot access constant COOKIE on string',
				10,
			],
			[
				'Cannot access constant COOKIE on class-string',
				14,
			],
			[
				'Cannot access constant FTC on class-string<DateTimeZone>',
				24,
			],
		]);
	}

}
