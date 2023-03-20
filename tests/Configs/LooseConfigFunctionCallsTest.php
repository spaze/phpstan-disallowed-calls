<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use Nette\Neon\Neon;
use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\Calls\FunctionCalls;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\IdentifierFormatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedRuleErrors;

class LooseConfigFunctionCallsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		// Load the configuration from this file
		$config = Neon::decode(file_get_contents(__DIR__ . '/../../disallowed-loose-calls.neon'));
		// emulate how the real config loader expands constants that are used in the config file above (e.g. ::ENT_QUOTES)
		foreach ($config['parameters']['disallowedFunctionCalls'] as &$call) {
			foreach (['allowParamsAnywhere', 'allowParamFlagsAnywhere'] as $key) {
				if (!isset($call[$key])) {
					continue;
				}
				foreach ($call[$key] as &$param) {
					if (is_string($param) && preg_match('/^::([A-Z0-9_]+)$/', $param, $matches)) {
						$param = constant($matches[1]);
					}
				}
			}
		}
		return new FunctionCalls(
			new DisallowedRuleErrors(new AllowedPath(new FileHelper(__DIR__))),
			new DisallowedCallFactory(new IdentifierFormatter()),
			$config['parameters']['disallowedFunctionCalls']
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/looseCalls.php'], [
			// expect these error messages, on these lines:
			['Calling in_array() is forbidden, set the third parameter $strict to `true` to also check the types to prevent type juggling bugs', 4],
			['Calling in_array() is forbidden, set the third parameter $strict to `true` to also check the types to prevent type juggling bugs', 6],
			['Calling htmlspecialchars() is forbidden, set the $flags parameter to `ENT_QUOTES` to also convert single quotes to entities to prevent some HTML injection bugs', 7],
			['Calling htmlspecialchars() is forbidden, set the $flags parameter to `ENT_QUOTES` to also convert single quotes to entities to prevent some HTML injection bugs', 12],
			['Calling htmlspecialchars() is forbidden, set the $flags parameter to `ENT_QUOTES` to also convert single quotes to entities to prevent some HTML injection bugs', 13],
		]);
	}

}
