<?php
declare(strict_types = 1);

namespace Interfaces;

interface BaseInterface
{

	public function x(): void;


	public static function y(): void;

}


final class Implementation implements BaseInterface
{

	public function x(): void
	{
	}


	public static function y(): void
	{
	}

}
