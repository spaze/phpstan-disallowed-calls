<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use Nette\Neon\Neon;
use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\Calls\MethodCalls;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class InsecureConfigMethodCallsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		// Load the configuration from this file
		$config = Neon::decode(file_get_contents(__DIR__ . '/../../disallowed-insecure-calls.neon'));
		$normalizer = new Normalizer();
		$formatter = new Formatter($normalizer);
		$allowed = new Allowed($formatter, $normalizer, new AllowedPath(new FileHelper(__DIR__)));
		return new MethodCalls(
			new DisallowedMethodRuleErrors(
				new DisallowedCallsRuleErrors($allowed),
				new TypeResolver(),
				$formatter
			),
			new DisallowedCallFactory($formatter, $normalizer, $allowed),
			$config['parameters']['disallowedMethodCalls']
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/insecureCalls.php'], [
			// expect these error messages, on these lines:
			['Calling mysqli::query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability', 23],
			['Calling mysqli::multi_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability', 24],
			['Calling mysqli::real_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability', 25],
		]);
	}

}
