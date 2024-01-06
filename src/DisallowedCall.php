<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfig;

class DisallowedCall implements DisallowedWithParams
{

	/**
	 * @param list<string> $excludes
	 * @param list<string> $definedIn
	 */
	public function __construct(
		private readonly string $call,
		private readonly array $excludes,
		private readonly array $definedIn,
		private readonly ?string $message,
		private readonly AllowedConfig $allowedConfig,
		private readonly ?string $errorIdentifier,
		private readonly ?string $errorTip,
	) {
	}


	public function getCall(): string
	{
		return $this->call;
	}


	/**
	 * @return list<string>
	 */
	public function getExcludes(): array
	{
		return $this->excludes;
	}


	/**
	 * @return list<string>
	 */
	public function getDefinedIn(): array
	{
		return $this->definedIn;
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


	public function getKey(): string
	{
		// The key consists of "initial" config values that would be overwritten with more specific details in a custom config.
		// `allowIn` & `allowParams*` & few others aren't included because these are set by the user in their config, not in the bundled files.
		return serialize([$this->getCall(), $this->getAllowExceptParams()]);
	}

}
