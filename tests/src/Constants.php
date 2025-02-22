<?php
declare(strict_types = 1);

namespace Constants;

use Attributes\AttributeClass;
use Attributes\AttributeColumn2;
use Attributes\AttributeColumn3;

class Constants
{
}

#[AttributeClass]
class ChildConstants extends Constants
{

	#[AttributeClass]
	public function leMethod(): void
	{
		FILTER_FLAG_EMAIL_UNICODE;
		FILTER_FLAG_ENCODE_HIGH;
		FILTER_FLAG_ALLOW_HEX;
		FILTER_FLAG_NO_ENCODE_QUOTES;
		FILTER_FLAG_ALLOW_OCTAL;
		FILTER_FLAG_ALLOW_FRACTION;
		FILTER_FLAG_ENCODE_AMP;
		FILTER_FLAG_ENCODE_LOW;
		FILTER_FLAG_IPV4;
		FILTER_FLAG_IPV6;
	}

	#[\AttributeClass2]
	private function method()
	{
	}

}

#[AttributeColumn2]
class Constants2
{

	#[AttributeColumn2]
	public function leMethod(): void
	{
		FILTER_FLAG_EMAIL_UNICODE;
		FILTER_FLAG_ENCODE_HIGH;
		FILTER_FLAG_ALLOW_HEX;
		FILTER_FLAG_NO_ENCODE_QUOTES;
		FILTER_FLAG_ALLOW_OCTAL;
		FILTER_FLAG_ALLOW_FRACTION;
		FILTER_FLAG_ENCODE_AMP;
		FILTER_FLAG_ENCODE_LOW;
		FILTER_FLAG_IPV4;
		FILTER_FLAG_IPV6;
	}

	#[AttributeColumn3]
	private function method()
	{
	}

}
