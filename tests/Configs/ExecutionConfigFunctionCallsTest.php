<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use Nette\Neon\Neon;
use PHPStan\File\FileHelper as PHPStanFileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\FunctionCalls;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\FileHelper;

class ExecutionConfigFunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		// Load the configuration from this file
		$config = Neon::decode(file_get_contents(__DIR__ . '/../../disallowed-execution-calls.neon'));
		return new FunctionCalls(
			new DisallowedHelper(new FileHelper(new PHPStanFileHelper(__DIR__))),
			new DisallowedCallFactory(),
			$config['parameters']['disallowedFunctionCalls']
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/executionCalls.php'], [
			// expect these error messages, on these lines:
			['Calling exec() is forbidden, because reasons', 4],
			['Calling passthru() is forbidden, because reasons', 5],
			['Calling proc_open() is forbidden, because reasons', 7],
			['Calling shell_exec() is forbidden, because reasons', 8],
			['Calling system() is forbidden, because reasons', 10],
			['Calling pcntl_exec() is forbidden, because reasons', 11],
			['Calling popen() is forbidden, because reasons', 12],
		]);
	}

}
