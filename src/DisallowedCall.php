<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParam;

class DisallowedCall
{

	/** @var string */
	private $call;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var string[] */
	private $allowInCalls;

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


	/**
	 * DisallowedCall constructor.
	 *
	 * @param string $call
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param string[] $allowInCalls
	 * @param array<int, DisallowedCallParam> $allowParamsInAllowed
	 * @param array<int, DisallowedCallParam> $allowParamsAnywhere
	 * @param array<int, DisallowedCallParam> $allowExceptParamsInAllowed
	 * @param array<int, DisallowedCallParam> $allowExceptParams
	 * @param string|null $errorIdentifier
	 */
	public function __construct(string $call, ?string $message, array $allowIn, array $allowInCalls, array $allowParamsInAllowed, array $allowParamsAnywhere, array $allowExceptParamsInAllowed, array $allowExceptParams, ?string $errorIdentifier)
	{
		$this->call = $call;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowInCalls = $allowInCalls;
		$this->allowParamsInAllowed = $allowParamsInAllowed;
		$this->allowParamsAnywhere = $allowParamsAnywhere;
		$this->allowExceptParamsInAllowed = $allowExceptParamsInAllowed;
		$this->allowExceptParams = $allowExceptParams;
		$this->errorIdentifier = $errorIdentifier;
	}


	public function getCall(): string
	{
		return $this->call;
	}


	public function getMessage(): string
	{
		return $this->message ?? 'because reasons';
	}


	/**
	 * @return string[]
	 */
	public function getAllowIn(): array
	{
		return $this->allowIn;
	}


	/**
	 * @return string[]
	 */
	public function getAllowInCalls(): array
	{
		return $this->allowInCalls;
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


	public function getKey(): string
	{
		// The key consists of "initial" config values that would be overwritten with more specific details in a custom config.
		// `allowIn` & `allowParams*` & few others aren't included because these are set by the user in their config, not in the bundled files.
		return serialize([$this->getCall(), $this->getAllowExceptParams()]);
	}

}
