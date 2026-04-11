<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Allowed\UsagePosition;

interface DisallowedWithTypeHints extends Disallowed
{

	public function getAllowInPosition(UsagePosition $position): bool;


	public function getAllowExceptInPosition(UsagePosition $position): bool;

}
