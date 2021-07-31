<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedCall
{

	/** @var string */
	private $call;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var array<integer, integer|boolean|string> */
	private $allowParamsInAllowed;

	/** @var array<integer, integer|boolean|string> */
	private $allowParamsAnywhere;

	/** @var array<integer, integer|boolean|string> */
	private $allowExceptParams;

	/** @var array<integer, integer|boolean|string> */
	private $allowExceptCaseInsensitiveParams;


	/**
	 * DisallowedCall constructor.
	 *
	 * @param string $call
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param array<integer, integer|boolean|string> $allowParamsInAllowed
	 * @param array<integer, integer|boolean|string> $allowParamsAnywhere
	 * @param array<integer, integer|boolean|string> $allowExceptParams
	 * @param array<integer, integer|boolean|string> $allowExceptCaseInsensitiveParams
	 */
	public function __construct(string $call, ?string $message, array $allowIn, array $allowParamsInAllowed, array $allowParamsAnywhere, array $allowExceptParams, array $allowExceptCaseInsensitiveParams)
	{
		$call = substr($call, -2) === '()' ? substr($call, 0, -2) : $call;
		$this->call = ltrim($call, '\\');
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowParamsInAllowed = $allowParamsInAllowed;
		$this->allowParamsAnywhere = $allowParamsAnywhere;
		$this->allowExceptParams = $allowExceptParams;
		$this->allowExceptCaseInsensitiveParams = $allowExceptCaseInsensitiveParams;
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
	 * @return array<integer, integer|boolean|string>
	 */
	public function getAllowParamsInAllowed(): array
	{
		return $this->allowParamsInAllowed;
	}


	/**
	 * @return array<integer, integer|boolean|string>
	 */
	public function getAllowParamsAnywhere(): array
	{
		return $this->allowParamsAnywhere;
	}


	/**
	 * @return array<integer, integer|boolean|string>
	 */
	public function getAllowExceptParams(): array
	{
		return $this->allowExceptParams;
	}


	/**
	 * @return array<integer, integer|boolean|string>
	 */
	public function getAllowExceptCaseInsensitiveParams(): array
	{
		return $this->allowExceptCaseInsensitiveParams;
	}


	public function getKey(): string
	{
		// The key consists of "initial" config values that would be overwritten with more specific details in a custom config.
		// `allowIn` & `allowParams*` aren't included because these are set by the user in their config, not in the bundled files.
		return serialize([$this->getCall(), $this->getAllowExceptParams(), $this->getAllowExceptCaseInsensitiveParams()]);
	}

}
