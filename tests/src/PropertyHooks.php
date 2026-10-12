<?php
declare(strict_types = 1);

namespace PropertyHooks;

class PropertyHooks
{

	public string $allowed {
		#[AllowMd5]
		get => md5('allowed');
	}

	public string $disallowed {
		get => md5('disallowed');
		set(string $value) {
			md5($value);
		}
	}

	public string $allowedExcept {
		#[DisallowSha1]
		get => sha1('disallowed');
		set(string $value) {
			sha1($value);
		}
	}

	#[AllowMd5]
	public string $propertyAttribute {
		get => md5('property attribute');
	}

	public string $allowedSet {
		get => 'allowed set';
		#[AllowMd5]
		set(string $value) {
			md5($value);
		}
	}

}
