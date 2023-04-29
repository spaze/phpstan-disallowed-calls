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

	/** @var list<string> */
	private $allowInClassWithAttributes;

	/** @var list<string> */
	private $allowExceptInClassWithAttributes;

	/** @var list<string> */
	private $allowInCallWithAttributes;

	/** @var list<string> */
	private $allowExceptInCallWithAttributes;

	/** @var list<string> */
	private $allowInAnyMethodWithAttributes;

	/** @var list<string> */
	private $allowExceptInAnyMethodWithAttributes;

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
	 * @param list<string> $allowInClassWithAttributes
	 * @param list<string> $allowExceptInClassWithAttributes
	 * @param list<string> $allowInCallWithAttributes
	 * @param list<string> $allowExceptInCallWithAttributes
	 * @param list<string> $allowInAnyMethodWithAttributes
	 * @param list<string> $allowExceptInAnyMethodWithAttributes
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
		array $allowInClassWithAttributes,
		array $allowExceptInClassWithAttributes,
		array $allowInCallWithAttributes,
		array $allowExceptInCallWithAttributes,
		array $allowInAnyMethodWithAttributes,
		array $allowExceptInAnyMethodWithAttributes,
		array $allowParamsInAllowed,
		array $allowParamsAnywhere,
		array $allowExceptParamsInAllowed,
		array $allowExceptParams
	) {
		$this->allowIn = $allowIn;
		$this->allowExceptIn = $allowExceptIn;
		$this->allowInCalls = $allowInCalls;
		$this->allowExceptInCalls = $allowExceptInCalls;
		$this->allowInClassWithAttributes = $allowInClassWithAttributes;
		$this->allowExceptInClassWithAttributes = $allowExceptInClassWithAttributes;
		$this->allowInCallWithAttributes = $allowInCallWithAttributes;
		$this->allowExceptInCallWithAttributes = $allowExceptInCallWithAttributes;
		$this->allowInAnyMethodWithAttributes = $allowInAnyMethodWithAttributes;
		$this->allowExceptInAnyMethodWithAttributes = $allowExceptInAnyMethodWithAttributes;
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
	 * @return list<string>
	 */
	public function getAllowInClassWithAttributes(): array
	{
		return $this->allowInClassWithAttributes;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowExceptInClassWithAttributes(): array
	{
		return $this->allowExceptInClassWithAttributes;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowInCallWithAttributes(): array
	{
		return $this->allowInCallWithAttributes;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowExceptInCallWithAttributes(): array
	{
		return $this->allowExceptInCallWithAttributes;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowInAnyMethodWithAttributes(): array
	{
		return $this->allowInAnyMethodWithAttributes;
	}


	/**
	 * @return list<string>
	 */
	public function getAllowExceptInAnyMethodWithAttributes(): array
	{
		return $this->allowExceptInAnyMethodWithAttributes;
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
