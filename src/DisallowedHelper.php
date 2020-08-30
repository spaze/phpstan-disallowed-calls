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
	 * @param string[] $config
	 * @return boolean
	 */
	public function isAllowed(Scope $scope, array $args, array $config): bool
	{
		foreach (($config['allowIn'] ?? []) as $allowedPath) {
			if (fnmatch($this->fileHelper->absolutizePath($allowedPath), $scope->getFile()) && $this->hasAllowedParams($scope, $args, $config, 'allowParamsInAllowed', true)) {
				return true;
			}
		}
		return $this->hasAllowedParams($scope, $args, $config, 'allowParamsAnywhere', false);
	}


	/**
	 * @param Scope $scope
	 * @param Arg[] $args
	 * @param string[] $config
	 * @param string $configKey
	 * @param boolean $default
	 * @return boolean
	 */
	private function hasAllowedParams(Scope $scope, array $args, array $config, string $configKey, bool $default): bool
	{
		if (isset($config[$configKey]) && is_array($config[$configKey])) {
			$disallowed = false;
			foreach ($config[$configKey] as $param => $value) {
				$arg = $args[$param - 1] ?? null;
				$type = $arg ? $scope->getType($arg->value) : null;
				if ($arg && $type instanceof ConstantScalarType) {
					$disallowed = $disallowed || ($value !== $type->getValue());
				} else {
					$disallowed = true;
				}
			}
			if (count($config[$configKey]) > 0) {
				return !$disallowed;
			}
		}
		return $default;
	}

}
