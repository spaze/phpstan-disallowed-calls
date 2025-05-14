<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\MethodCalls;

/**
 * @extends RuleTestCase<MethodCalls>
 */
class InsecureConfigMethodCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(MethodCalls::class);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/insecureCalls.php'], [
			// expect these error messages, on these lines:
			['Calling mysqli::query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability.', 23],
			['Calling mysqli::multi_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability.', 24],
			['Calling mysqli::real_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability.', 25],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
			__DIR__ . '/../../disallowed-insecure-calls.neon',
		];
	}

}
