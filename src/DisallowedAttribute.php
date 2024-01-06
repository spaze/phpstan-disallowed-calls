<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfig;

class DisallowedAttribute implements DisallowedWithParams
{

	/**
	 * @param list<string> $excludes
	 */
	public function __construct(
		private readonly string $attribute,
		private readonly array $excludes,
		private readonly ?string $message,
		private readonly AllowedConfig $allowedConfig,
		private readonly ?string $errorIdentifier,
		private readonly ?string $errorTip,
	) {
	}


	public function getAttribute(): string
	{
		return $this->attribute;
	}


	/**
	 * @return list<string>
	 */
	public function getExcludes(): array
	{
		return $this->excludes;
	}


	public function getMessage(): ?string
	{
		return $this->message;
	}


	/** @inheritDoc */
	public function getAllowIn(): array
	{
		return $this->allowedConfig->getAllowIn();
	}


	/** @inheritDoc */
	public function getAllowExceptIn(): array
	{
		return $this->allowedConfig->getAllowExceptIn();
	}


	public function getAllowInCalls(): array
	{
		return $this->allowedConfig->getAllowInCalls();
	}


	public function getAllowExceptInCalls(): array
	{
		return $this->allowedConfig->getAllowExceptInCalls();
	}


	public function getAllowParamsInAllowed(): array
	{
		return $this->allowedConfig->getAllowParamsInAllowed();
	}


	public function getAllowParamsAnywhere(): array
	{
		return $this->allowedConfig->getAllowParamsAnywhere();
	}


	public function getAllowExceptParamsInAllowed(): array
	{
		return $this->allowedConfig->getAllowExceptParamsInAllowed();
	}


	public function getAllowExceptParams(): array
	{
		return $this->allowedConfig->getAllowExceptParams();
	}


	public function getErrorIdentifier(): ?string
	{
		return $this->errorIdentifier;
	}


	public function getErrorTip(): ?string
	{
		return $this->errorTip;
	}

}
