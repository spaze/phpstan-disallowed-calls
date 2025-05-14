<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\FunctionCalls;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class ExecutionConfigFunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(FunctionCalls::class);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/executionCalls.php'], [
			// expect these error messages, on these lines:
			['Calling exec() is forbidden.', 4],
			['Calling passthru() is forbidden.', 5],
			['Calling proc_open() is forbidden.', 7],
			['Calling shell_exec() is forbidden.', 8],
			['Calling system() is forbidden.', 10],
			['Calling pcntl_exec() is forbidden.', 11],
			['Calling popen() is forbidden.', 12],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
			__DIR__ . '/../../disallowed-execution-calls.neon',
		];
	}

}
