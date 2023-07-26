<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use Spaze\PHPStan\Rules\Disallowed\Params\Param;

class AllowedConfig
{

	/** @var list<string> */
	private $allowIn;

	/** @var list<string> */
	private $allowExceptIn;

	/** @var list<string> */
	private $allowInCalls;

	/** @var list<string> */
	private $allowExceptInCalls;

	/** @var array<int|string, Param> */
	private $allowParamsInAllowed;

	/** @var array<int|string, Param> */
	private $allowParamsAnywhere;

	/** @var array<int|string, Param> */
	private $allowExceptParamsInAllowed;

	/** @var array<int|string, Param> */
	private $allowExceptParams;


	/**
	 * @param list<string> $allowIn
	 * @param list<string> $allowExceptIn
	 * @param list<string> $allowInCalls
	 * @param list<string> $allowExceptInCalls
	 * @param array<int|string, Param> $allowParamsInAllowed
	 * @param array<int|string, Param> $allowParamsAnywhere
	 * @param array<int|string, Param> $allowExceptParamsInAllowed
	 * @param array<int|string, Param> $allowExceptParams
	 */
	public function __construct(
		array $allowIn,
		array $allowExceptIn,
		array $allowInCalls,
		array $allowExceptInCalls,
		array $allowParamsInAllowed,
		array $allowParamsAnywhere,
		array $allowExceptParamsInAllowed,
		array $allowExceptParams
	) {
		$this->allowIn = $allowIn;
		$this->allowExceptIn = $allowExceptIn;
		$this->allowInCalls = $allowInCalls;
		$this->allowExceptInCalls = $allowExceptInCalls;
		$this->allowParamsInAllowed = $allowParamsInAllowed;
		$this->allowParamsAnywhere = $allowParamsAnywhere;
		$this->allowExceptParamsInAllowed = $allowExceptParamsInAllowed;
		$this->allowExceptParams = $allowExceptParams;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowIn(): array
	{
		return $this->allowIn;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowExceptIn(): array
	{
		return $this->allowExceptIn;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowInCalls(): array
	{
		return $this->allowInCalls;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowExceptInCalls(): array
	{
		return $this->allowExceptInCalls;
	}


	/**
	 * @return array<int|string, Param>
	 */
	public function getAllowParamsInAllowed(): array
	{
		return $this->allowParamsInAllowed;
	}


	/**
	 * @return array<int|string, Param>
	 */
	public function getAllowParamsAnywhere(): array
	{
		return $this->allowParamsAnywhere;
	}


	/**
	 * @return array<int|string, Param>
	 */
	public function getAllowExceptParamsInAllowed(): array
	{
		return $this->allowExceptParamsInAllowed;
	}


	/**
	 * @return array<int|string, Param>
	 */
	public function getAllowExceptParams(): array
	{
		return $this->allowExceptParams;
	}

}
