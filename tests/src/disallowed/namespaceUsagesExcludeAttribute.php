<?php
declare(strict_types = 1);

namespace NoBigDeal;

use PrivateModule\PrivateClass;
use PrivateModule\PublicClass;

class Service extends Base implements SomeInterface
{
	public function usePublicClass()
	{
		return new PublicClass();
	}

	public function usePrivateClass()
	{
		return new PrivateClass();
	}

}
