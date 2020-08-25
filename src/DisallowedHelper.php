<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node\Arg;
use PhpParser\Node\Scalar;
use PHPStan\File\FileHelper;

class DisallowedHelper
{

	/** @var FileHelper */
	private $fileHelper;


	public function __construct(FileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}


	/**
	 * @param string $file
	 * @param Arg[] $args
	 * @param string[] $config
	 * @return boolean
	 */
	public function isAllowed(string $file, array $args, array $config): bool
	{
		foreach (($config['allowIn'] ?? []) as $allowedPath) {
			if (fnmatch($this->fileHelper->absolutizePath($allowedPath), $file) && $this->hasAllowedParams($config, $args, 'allowParamsInAllowed', true)) {
				return true;
			}
		}
		return $this->hasAllowedParams($config, $args, 'allowParamsAnywhere', false);
	}


	/**
	 * @param string[] $config
	 * @param Arg[] $args
	 * @param string $configKey
	 * @param boolean $default
	 * @return boolean
	 */
	private function hasAllowedParams(array $config, array $args, string $configKey, bool $default): bool
	{
		if (isset($config[$configKey]) && is_array($config[$configKey])) {
			$disallowed = false;
			foreach ($config[$configKey] as $param => $value) {
				if (isset($args[$param - 1])) {
					$arg = $args[$param - 1];
					$disallowed = $disallowed || $this->isDisallowedParam($value, $arg);
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


	/**
	 * @param mixed $value
	 * @param Arg $arg
	 * @return boolean
	 */
	private function isDisallowedParam($value, Arg $arg): bool
	{
		// 2nd param in print_r(..., true) is returned as Node\Expr\ConstFetch by the parser and I can't find a way to
		// get it as a bool, only as a string. So to support booleans in the .neon config file we have to convert to a string manually.
		if (is_bool($value)) {
			return $this->isDisallowedParam($value ? 'true' : 'false', $arg);
		} else {
			return ($value !== ($arg->value instanceof Scalar ? $arg->value->value : (string)$arg->value->name));
		}
	}

}
