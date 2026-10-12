<?php
declare(strict_types = 1);

namespace InnerFunctions;

class InnerFunctions
{

	public function method(): void
	{
		function innerInMethod(): void
		{
			md5('inner in method');
			sha1('inner in method');
		}
	}

}

function outer(): void
{
	#[AllowMd5]
	#[DisallowSha1]
	function innerWithAttributes(): void
	{
		md5('inner with attributes');
		sha1('inner with attributes');
	}
}

#[AllowMd5]
#[DisallowSha1]
function topLevel(): void
{
	md5('top level');
	sha1('top level');
}

function afterInnerFunctions(): void
{
	md5('after inner functions');
	sha1('after inner functions');
}
