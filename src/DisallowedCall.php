<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParam;

class DisallowedCall implements Disallowed
{

	/** @var string */
	private $call;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var string[] */
	private $allowExceptIn;

	/** @var string[] */
	private $allowInCalls;

	/** @var string[] */
	private $allowExceptInCalls;

	/** @var array<int, DisallowedCallParam> */
	private $allowParamsInAllowed;

	/** @var array<int, DisallowedCallParam> */
	private $allowParamsAnywhere;

	/** @var array<int, DisallowedCallParam> */
	private $allowExceptParamsInAllowed;

	/** @var array<int, DisallowedCallParam> */
	private $allowExceptParams;

	/** @var string|null */
	private $errorIdentifier;

	/** @var string|null */
	private $errorTip;


	/**
	 * @param string $call
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param string[] $allowExceptIn
	 * @param string[] $allowInCalls
	 * @param string[] $allowExceptInCalls
	 * @param array<int, DisallowedCallParam> $allowParamsInAllowed
	 * @param array<int, DisallowedCallParam> $allowParamsAnywhere
	 * @param array<int, DisallowedCallParam> $allowExceptParamsInAllowed
	 * @param array<int, DisallowedCallParam> $allowExceptParams
	 * @param string|null $errorIdentifier
	 * @param string|null $errorTip
	 */
	public function __construct(
		string $call,
		?string $message,
		array $allowIn,
		array $allowExceptIn,
		array $allowInCalls,
		array $allowExceptInCalls,
		array $allowParamsInAllowed,
		array $allowParamsAnywhere,
		array $allowExceptParamsInAllowed,
		array $allowExceptParams,
		?string $errorIdentifier,
		?string $errorTip
	) {
		$this->call = $call;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowExceptIn = $allowExceptIn;
		$this->allowInCalls = $allowInCalls;
		$this->allowExceptInCalls = $allowExceptInCalls;
		$this->allowParamsInAllowed = $allowParamsInAllowed;
		$this->allowParamsAnywhere = $allowParamsAnywhere;
		$this->allowExceptParamsInAllowed = $allowExceptParamsInAllowed;
		$this->allowExceptParams = $allowExceptParams;
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


	/**
	 * @return string[]
	 */
	public function getAllowInCalls(): array
	{
		return $this->allowInCalls;
	}


	/**
	 * @return string[]
	 */
	public function getAllowExceptInCalls(): array
	{
		return $this->allowExceptInCalls;
	}


	/**
	 * @return array<int, DisallowedCallParam>
	 */
	public function getAllowParamsInAllowed(): array
	{
		return $this->allowParamsInAllowed;
	}


	/**
	 * @return array<int, DisallowedCallParam>
	 */
	public function getAllowParamsAnywhere(): array
	{
		return $this->allowParamsAnywhere;
	}


	/**
	 * @return array<int, DisallowedCallParam>
	 */
	public function getAllowExceptParamsInAllowed(): array
	{
		return $this->allowExceptParamsInAllowed;
	}


	/**
	 * @return array<int, DisallowedCallParam>
	 */
	public function getAllowExceptParams(): array
	{
		return $this->allowExceptParams;
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
