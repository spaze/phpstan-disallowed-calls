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
use Spaze\PHPStan\Rules\Disallowed\Formatter\MethodFormatter;
use Spaze\PHPStan\Rules\Disallowed\IdentifierFormatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedRuleErrors;
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
		return new MethodCalls(
			new DisallowedMethodRuleErrors(
				new DisallowedRuleErrors(new Allowed(new MethodFormatter(), new AllowedPath(new FileHelper(__DIR__)))),
				new TypeResolver(),
				new IdentifierFormatter(),
				new MethodFormatter()
			),
			new DisallowedCallFactory(new IdentifierFormatter()),
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
