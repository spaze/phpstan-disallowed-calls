<?php
declare(strict_types = 1);

namespace Waldo\Foo;

class BarBase
{

	public function inHierarchy(): void
	{
		str_starts_with('foo', 'forbidden');
		str_starts_with('foo', 'allowed');
		str_ends_with('foo', 'forbidden');
		str_ends_with('foo', 'allowed');
		str_contains('foo', 'allowed_param');
		str_contains('foo', 'other');
	}

}

class BarBaseChild extends BarBase
{

	public function inHierarchy(): void
	{
		str_starts_with('foo', 'forbidden');
		str_starts_with('foo', 'allowed');
		str_ends_with('foo', 'forbidden');
		str_ends_with('foo', 'allowed');
		str_contains('foo', 'allowed_param');
		str_contains('foo', 'other');
	}

}

// outside the hierarchy: str_ends_with and str_contains calls are allowed regardless of params; str_starts_with is still disallowed (allowInInstanceOf)
class BarOutside
{

	public function outsideHierarchy(): void
	{
		str_starts_with('foo', 'forbidden');
		str_starts_with('foo', 'allowed');
		str_ends_with('foo', 'forbidden');
		str_ends_with('foo', 'allowed');
		str_contains('foo', 'allowed_param');
		str_contains('foo', 'other');
	}

}

// test allowInInstanceOf + allowParamsInAllowed: allowed in hierarchy only when param is 'allowed_chars'
class BarBaseForAllowParams extends BarBase
{

	public function inHierarchy(): void
	{
		ltrim('foo', 'allowed_chars');
		ltrim('foo', 'other');
	}

}

// outside the hierarchy: all ltrim calls are disallowed (allowInInstanceOf)
class BarOutsideForAllowParams
{

	public function outsideHierarchy(): void
	{
		ltrim('foo', 'allowed_chars');
		ltrim('foo', 'other');
	}

}

class BarBaseChildForAllowParams extends BarBaseForAllowParams
{

	public function inHierarchy(): void
	{
		ltrim('foo', 'allowed_chars');
		ltrim('foo', 'other');
	}

}
