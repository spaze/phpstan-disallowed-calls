<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\FunctionCalls;

class DangerousConfigFunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(FunctionCalls::class);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/dangerousCalls.php'], [
			// expect these error messages, on these lines:
			['Calling apache_setenv() is forbidden, might overwrite existing variables.', 4],
			['Calling dl() is forbidden, removed from most SAPIs, might load untrusted code.', 5],
			['Calling extract() is forbidden, do not use extract() and especially not on untrusted data.', 7],
			['Calling posix_getpwuid() is forbidden, might reveal system user information.', 8],
			['Calling posix_kill() is forbidden, do not send signals to processes from the script.', 9],
			['Calling posix_mkfifo() is forbidden, do not create named pipes in the script.', 10],
			['Calling posix_mknod() is forbidden, do not create special files in the script.', 11],
			['Calling highlight_file() is forbidden, might reveal source code or config files.', 12],
			['Calling show_source() is forbidden, might reveal source code or config files (alias of highlight_file()).', 13],
			['Calling pfsockopen() is forbidden, use fsockopen() to create non-persistent socket connections.', 14],
			['Calling print_r() is forbidden, use some logger instead.', 15],
			['Calling print_r() is forbidden, use some logger instead.', 17],
			['Calling proc_nice() is forbidden, changes the priority of the current process.', 18],
			['Calling putenv() is forbidden, might overwrite existing variables.', 19],
			['Calling socket_create_listen() is forbidden, do not accept new socket connections in the PHP script.', 20],
			['Calling socket_listen() is forbidden, do not accept new socket connections in the PHP script.', 21],
			['Calling var_dump() is forbidden, use some logger instead.', 22],
			['Calling var_export() is forbidden, use some logger instead.', 23],
			['Calling var_export() is forbidden, use some logger instead.', 25],
			['Calling phpinfo() is forbidden, might reveal session id or other tokens in cookies.', 26, 'see https://www.michalspacek.com/stealing-session-ids-with-phpinfo-and-how-to-stop-it and use e.g. spaze/phpinfo instead'],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
			__DIR__ . '/../../disallowed-dangerous-calls.neon',
		];
	}

}
