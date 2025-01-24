<?php
declare(strict_types = 1);

function disallowedFunction()
{
}

class Bug288DocblockTemplateCallableParam
{

	public function testItDoesNothing(): void
	{
		// the $expected param here is a string param, not a callable param, no error should be reported here
		$this->assertSame('disallowedFunction', 'whatever');
		$this->constantStringIsCallable('disallowedFunction');
	}

	/**
	 * A copy of PHPUnit\Framework\Assert::assertSame()'s docblock
	 *
	 * @template ExpectedType
	 * @param ExpectedType $expected
	 * @phpstan-assert =ExpectedType $actual
	 */
	public function assertSame(mixed $expected, mixed $actual, string $message = ''): void
	{
	}


	/**
	 * @param 'disallowedFunction' $callable
	 */
	public function constantStringIsCallable(string $callable)
	{
	}

}
