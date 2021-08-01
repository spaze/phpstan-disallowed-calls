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

	/** @var array<integer, DisallowedCallParam> */
	private $allowParamsInAllowed;

	/** @var array<integer, DisallowedCallParam> */
	private $allowParamsAnywhere;

	/** @var array<integer, DisallowedCallParam> */
	private $allowExceptParams;


	/**
	 * DisallowedCall constructor.
	 *
	 * @param string $call
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param array<integer, DisallowedCallParam> $allowParamsInAllowed
	 * @param array<integer, DisallowedCallParam> $allowParamsAnywhere
	 * @param array<integer, DisallowedCallParam> $allowExceptParams
	 */
	public function __construct(string $call, ?string $message, array $allowIn, array $allowParamsInAllowed, array $allowParamsAnywhere, array $allowExceptParams)
	{
		$call = substr($call, -2) === '()' ? substr($call, 0, -2) : $call;
		$this->call = ltrim($call, '\\');
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowParamsInAllowed = $allowParamsInAllowed;
		$this->allowParamsAnywhere = $allowParamsAnywhere;
		$this->allowExceptParams = $allowExceptParams;
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
	 * @return array<integer, DisallowedCallParam>
	 */
	public function getAllowParamsInAllowed(): array
	{
		return $this->allowParamsInAllowed;
	}


	/**
	 * @return array<integer, DisallowedCallParam>
	 */
	public function getAllowParamsAnywhere(): array
	{
		return $this->allowParamsAnywhere;
	}


	/**
	 * @return array<integer, DisallowedCallParam>
	 */
	public function getAllowExceptParams(): array
	{
		return $this->allowExceptParams;
	}


	public function getKey(): string
	{
		// The key consists of "initial" config values that would be overwritten with more specific details in a custom config.
		// `allowIn` & `allowParams*` aren't included because these are set by the user in their config, not in the bundled files.
		return serialize([$this->getCall(), $this->getAllowExceptParams()]);
	}

}
