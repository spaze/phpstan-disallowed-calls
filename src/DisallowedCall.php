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


	/**
	 * DisallowedCall constructor.
	 *
	 * @param string $call
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param array<integer, integer|boolean|string> $allowParamsInAllowed
	 * @param array<integer, integer|boolean|string> $allowParamsAnywhere
	 */
	public function __construct(string $call, ?string $message, array $allowIn, array $allowParamsInAllowed, array $allowParamsAnywhere)
	{
		$this->call = $call;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowParamsInAllowed = $allowParamsInAllowed;
		$this->allowParamsAnywhere = $allowParamsAnywhere;
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

}
