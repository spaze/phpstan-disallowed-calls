<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Configs;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\FunctionCalls;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class NonTimingSafeConfigFunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(FunctionCalls::class);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/configs/nonTimingSafeCalls.php'], [
			// expect these error messages, on these lines:
			['Calling hex2bin() is forbidden, it is not timing-safe, use sodium_hex2bin() instead.', 4, 'ext-sodium is bundled with PHP since 7.2 but not always enabled, use ParagonIE\ConstantTime\Hex::decode() if not available'],
			['Calling bin2hex() is forbidden, it is not timing-safe, use sodium_bin2hex() instead.', 5, 'ext-sodium is bundled with PHP since 7.2 but not always enabled, use ParagonIE\ConstantTime\Hex::encode() if not available'],
			['Calling base64_decode() is forbidden, it is not timing-safe, use sodium_base642bin() instead.', 6, 'ext-sodium is bundled with PHP since 7.2 but not always enabled, use ParagonIE\ConstantTime\Base64::decode() if not available'],
			['Calling base64_encode() is forbidden, it is not timing-safe, use sodium_bin2base64() instead.', 7, 'ext-sodium is bundled with PHP since 7.2 but not always enabled, use ParagonIE\ConstantTime\Base64::encode() if not available'],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
			__DIR__ . '/../../disallowed-non-timing-safe-calls.neon',
		];
	}

}
