<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Type;

interface DisallowedCallParam
{

	public function matches(Type $type): bool;


	public function getPosition(): ?int;


	public function getName(): ?string;


	/**
	 * @return int|bool|string|null
	 */
	public function getValue();

}
