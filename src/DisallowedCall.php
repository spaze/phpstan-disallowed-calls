<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedCall implements DisallowedWithParams
{

	/** @var string */
	private $call;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var string[] */
	private $allowExceptIn;

	/** @var AllowedConfig */
	private $allowedConfig;

	/** @var string|null */
	private $errorIdentifier;

	/** @var string|null */
	private $errorTip;


	/**
	 * @param string $call
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param string[] $allowExceptIn
	 * @param AllowedConfig $allowedConfig
	 * @param string|null $errorIdentifier
	 * @param string|null $errorTip
	 */
	public function __construct(
		string $call,
		?string $message,
		array $allowIn,
		array $allowExceptIn,
		AllowedConfig $allowedConfig,
		?string $errorIdentifier,
		?string $errorTip
	) {
		$this->call = $call;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowExceptIn = $allowExceptIn;
		$this->allowedConfig = $allowedConfig;
		$this->errorIdentifier = $errorIdentifier;
		$this->errorTip = $errorTip;
	}


	public function getCall(): string
	{
		return $this->call;
	}


	public function getMessage(): string
	{
		return $this->message ?? 'because reasons';
	}


	/** @inheritDoc */
	public function getAllowIn(): array
	{
		return $this->allowIn;
	}


	/** @inheritDoc */
	public function getAllowExceptIn(): array
	{
		return $this->allowExceptIn;
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
