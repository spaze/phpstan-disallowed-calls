<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

interface DisallowedVariableFactory
{

	/**
	 * @param array<array{variable?:string, message?:string, allowIn?:string[], allowExceptIn?:string[], disallowIn?:string[], errorIdentifier?:string}> $config
	 * @return DisallowedVariable[]
	 */
	public function getDisallowedVariables(array $config): array;

}
