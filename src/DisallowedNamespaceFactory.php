<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedNamespaceFactory
{

	/** @var Normalizer */
	private $normalizer;


	public function __construct(Normalizer $normalizer)
	{
		$this->normalizer = $normalizer;
	}


	/**
	 * @param array<array{namespace?:string|list<string>, class?:string|list<string>, exclude?:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, errorIdentifier?:string, errorTip?:string}> $config
	 * @return list<DisallowedNamespace>
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedNamespaces = [];
		foreach ($config as $disallowed) {
			$namespaces = $disallowed['namespace'] ?? $disallowed['class'] ?? null;
			unset($disallowed['namespace'], $disallowed['class']);
			if (!$namespaces) {
				throw new ShouldNotHappenException("Either 'namespace' or 'class' must be set in configuration items");
			}
			$excludes = [];
			foreach ((array)($disallowed['exclude'] ?? []) as $exclude) {
				$excludes[] = $this->normalizer->normalizeNamespace($exclude);
			}
			foreach ((array)$namespaces as $namespace) {
				$disallowedNamespace = new DisallowedNamespace(
					$this->normalizer->normalizeNamespace($namespace),
					$excludes,
					$disallowed['message'] ?? null,
					$disallowed['allowIn'] ?? [],
					$disallowed['allowExceptIn'] ?? $disallowed['disallowIn'] ?? [],
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? null
				);
				$disallowedNamespaces[$disallowedNamespace->getNamespace()] = $disallowedNamespace;
			}
		}
		return array_values($disallowedNamespaces);
	}

}
