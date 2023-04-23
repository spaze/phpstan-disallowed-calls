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
use Spaze\PHPStan\Rules\Disallowed\Calls\ShellExecCalls;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedRuleErrors;

class ExecutionConfigShellExecCallsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		// Load the configuration from this file
		$config = Neon::decode(file_get_contents(__DIR__ . '/../../disallowed-execution-calls.neon'));
		$formatter = new Formatter();
		$normalizer = new Normalizer();
		$allowed = new Allowed($formatter, $normalizer, new AllowedPath(new FileHelper(__DIR__)));
		return new ShellExecCalls(
			new DisallowedRuleErrors($allowed),
			new DisallowedCallFactory($formatter, $normalizer, $allowed),
			$config['parameters']['disallowedFunctionCalls']
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/executionCalls.php'], [
			// expect these error messages, on these lines:
			['Using the backtick operator (`...`) is forbidden because shell_exec() is forbidden, because reasons', 9],
		]);
	}

}
