<?php
declare(strict_types = 1);

namespace NoBigDeal;

use Attributes\AttributeClass;

class Service
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

interface PrivateClass
{
}

#[AttributeClass]
class PublicClass
{
}
