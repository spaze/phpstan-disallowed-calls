<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\FunctionCalls;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class InsecureConfigFunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(FunctionCalls::class);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/insecureCalls.php'], [
			// expect these error messages, on these lines:
			['Calling md5() is forbidden, use hash() with at least SHA-256 for secure hash, or password_hash() for passwords.', 4],
			['Calling sha1() is forbidden, use hash() with at least SHA-256 for secure hash, or password_hash() for passwords.', 5],
			['Calling md5_file() is forbidden, use hash_file() with at least SHA-256 for secure hash.', 6],
			['Calling sha1_file() is forbidden, use hash_file() with at least SHA-256 for secure hash.', 7],
			['Calling hash() is forbidden, use hash() with at least SHA-256 for secure hash, or password_hash() for passwords.', 8],
			['Calling hash() is forbidden, use hash() with at least SHA-256 for secure hash, or password_hash() for passwords.', 9],
			['Calling hash_file() is forbidden, use hash_file() with at least SHA-256 for secure hash, or password_hash() for passwords.', 11],
			['Calling hash_file() is forbidden, use hash_file() with at least SHA-256 for secure hash, or password_hash() for passwords.', 12],
			['Calling hash_init() is forbidden, use hash_init() with at least SHA-256 for secure hash, or password_hash() for passwords.', 14],
			['Calling hash_init() is forbidden, use hash_init() with at least SHA-256 for secure hash, or password_hash() for passwords.', 15],
			['Calling mysql_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability.', 17],
			['Calling mysql_unbuffered_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability.', 18],
			['Calling mysqli_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability.', 19],
			['Calling mysqli_multi_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability.', 20],
			['Calling mysqli_real_query() is forbidden, use PDO::prepare() with variable binding/parametrized queries to prevent SQL Injection vulnerability.', 21],
			['Calling rand() is forbidden, it is not a cryptographically secure generator, use random_int() instead.', 27],
			['Calling mt_rand() is forbidden, it is not a cryptographically secure generator, use random_int() instead.', 28],
			['Calling lcg_value() is forbidden, it is not a cryptographically secure generator, use random_int() instead.', 29],
			['Calling uniqid() is forbidden, it is not a cryptographically secure generator, use random_bytes() instead.', 30],
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
