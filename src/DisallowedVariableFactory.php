<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

interface DisallowedVariableFactory
{

	/**
	 * @param array<array{variable?:string, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, errorIdentifier?:string}> $config
	 * @return list<DisallowedVariable>
	 */
	public function getDisallowedVariables(array $config): array;

}
