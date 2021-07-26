<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper as PHPStanFileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\FileHelper;

class ShellExecCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new ShellExecCalls(
			new DisallowedHelper(new FileHelper(new PHPStanFileHelper(__DIR__))),
			[
				[
					'function' => 'shell_*()',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/functionCalls.php'], [
			[
				'Using the backtick operator (`...`) is forbidden because shell_exec() is forbidden, because reasons [shell_exec() matches shell_*()]',
				46,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCalls.php'], []);
	}

}
