<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node\Arg;
use PHPStan\Analyser\Scope;
use PHPStan\File\FileHelper;
use PHPStan\Type\ConstantScalarType;

class DisallowedHelper
{

	/** @var FileHelper */
	private $fileHelper;


	public function __construct(FileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}


	/**
	 * @param Scope $scope
	 * @param Arg[] $args
	 * @param array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>} $config
	 * @return boolean
	 */
	public function isAllowed(Scope $scope, array $args, array $config): bool
	{
		foreach (($config['allowIn'] ?? []) as $allowedPath) {
			if (fnmatch($this->fileHelper->absolutizePath($allowedPath), $scope->getFile())
				&& $this->hasAllowedParams($scope, $args, $config['allowParamsInAllowed'] ?? null, true)
			) {
				return true;
			}
		}
		return $this->hasAllowedParams($scope, $args, $config['allowParamsAnywhere'] ?? null, false);
	}


	/**
	 * @param Scope $scope
	 * @param Arg[] $args
	 * @param array<integer, integer|boolean|string>|null $allowConfig
	 * @param boolean $default
	 * @return boolean
	 */
	private function hasAllowedParams(Scope $scope, array $args, ?array $allowConfig, bool $default): bool
	{
		if ($allowConfig !== null) {
			$disallowed = false;
			foreach ($allowConfig as $param => $value) {
				$arg = $args[$param - 1] ?? null;
				$type = $arg ? $scope->getType($arg->value) : null;
				if ($arg && $type instanceof ConstantScalarType) {
					$disallowed = $disallowed || ($value !== $type->getValue());
				} else {
					$disallowed = true;
				}
			}
			if (count($allowConfig) > 0) {
				return !$disallowed;
			}
		}
		return $default;
	}

}
