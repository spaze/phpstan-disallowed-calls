<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParam;

class AllowedConfig
{

	/** @var string[] */
	private $allowInCalls;

	/** @var string[] */
	private $allowExceptInCalls;

	/** @var array<int|string, DisallowedCallParam> */
	private $allowParamsInAllowed;

	/** @var array<int|string, DisallowedCallParam> */
	private $allowParamsAnywhere;

	/** @var array<int|string, DisallowedCallParam> */
	private $allowExceptParamsInAllowed;

	/** @var array<int|string, DisallowedCallParam> */
	private $allowExceptParams;


	/**
	 * @param string[] $allowInCalls
	 * @param string[] $allowExceptInCalls
	 * @param array<int|string, DisallowedCallParam> $allowParamsInAllowed
	 * @param array<int|string, DisallowedCallParam> $allowParamsAnywhere
	 * @param array<int|string, DisallowedCallParam> $allowExceptParamsInAllowed
	 * @param array<int|string, DisallowedCallParam> $allowExceptParams
	 */
	public function __construct(
		array $allowInCalls,
		array $allowExceptInCalls,
		array $allowParamsInAllowed,
		array $allowParamsAnywhere,
		array $allowExceptParamsInAllowed,
		array $allowExceptParams
	) {
		$this->allowInCalls = $allowInCalls;
		$this->allowExceptInCalls = $allowExceptInCalls;
		$this->allowParamsInAllowed = $allowParamsInAllowed;
		$this->allowParamsAnywhere = $allowParamsAnywhere;
		$this->allowExceptParamsInAllowed = $allowExceptParamsInAllowed;
		$this->allowExceptParams = $allowExceptParams;
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
	 * @return array<int|string, DisallowedCallParam>
	 */
	public function getAllowParamsInAllowed(): array
	{
		return $this->allowParamsInAllowed;
	}


	/**
	 * @return array<int|string, DisallowedCallParam>
	 */
	public function getAllowParamsAnywhere(): array
	{
		return $this->allowParamsAnywhere;
	}


	/**
	 * @return array<int|string, DisallowedCallParam>
	 */
	public function getAllowExceptParamsInAllowed(): array
	{
		return $this->allowExceptParamsInAllowed;
	}


	/**
	 * @return array<int|string, DisallowedCallParam>
	 */
	public function getAllowExceptParams(): array
	{
		return $this->allowExceptParams;
	}

}
