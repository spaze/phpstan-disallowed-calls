<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\FunctionCalls;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class LooseConfigFunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(FunctionCalls::class);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/looseCalls.php'], [
			// expect these error messages, on these lines:
			['Calling in_array() is forbidden, set the third parameter $strict to `true` to also check the types to prevent type juggling bugs.', 4],
			['Calling in_array() is forbidden, set the third parameter $strict to `true` to also check the types to prevent type juggling bugs.', 6],
			['Calling htmlspecialchars() is forbidden, set the $flags parameter to `ENT_QUOTES` to also convert single quotes to entities to prevent some HTML injection bugs.', 7],
			['Calling htmlspecialchars() is forbidden, set the $flags parameter to `ENT_QUOTES` to also convert single quotes to entities to prevent some HTML injection bugs.', 12],
			['Calling htmlspecialchars() is forbidden, set the $flags parameter to `ENT_QUOTES` to also convert single quotes to entities to prevent some HTML injection bugs.', 13],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
			__DIR__ . '/../../disallowed-loose-calls.neon',
		];
	}

}
