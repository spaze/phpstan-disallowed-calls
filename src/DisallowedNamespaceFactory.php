<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfigFactory;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class DisallowedNamespaceFactory
{

	private Formatter $formatter;

	private Normalizer $normalizer;

	private AllowedConfigFactory $allowedConfigFactory;


	public function __construct(Formatter $formatter, Normalizer $normalizer, AllowedConfigFactory $allowedConfigFactory)
	{
		$this->formatter = $formatter;
		$this->normalizer = $normalizer;
		$this->allowedConfigFactory = $allowedConfigFactory;
	}


	/**
	 * @param array<array{namespace?:string|list<string>, class?:string|list<string>, exclude?:string|list<string>, excludeWithAttribute?:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, allowInUse?:bool, errorIdentifier?:string, errorTip?:string}> $config
	 * @return list<DisallowedNamespace>
	 * @throws ShouldNotHappenException
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
			$excludeWithAttributes = [];
			foreach ((array)($disallowed['excludeWithAttribute'] ?? []) as $excludeWithAttribute) {
				$excludeWithAttributes[] = $this->normalizer->normalizeNamespace($excludeWithAttribute);
			}
			$namespaces = (array)$namespaces;
			try {
				foreach ($namespaces as $namespace) {
					$disallowedNamespace = new DisallowedNamespace(
						$this->normalizer->normalizeNamespace($namespace),
						$excludes,
						$excludeWithAttributes,
						$disallowed['message'] ?? null,
						$this->allowedConfigFactory->getConfig($disallowed),
						$disallowed['allowInUse'] ?? false,
						$disallowed['errorIdentifier'] ?? null,
						$disallowed['errorTip'] ?? null
					);
					$disallowedNamespaces[$disallowedNamespace->getNamespace()] = $disallowedNamespace;
				}
			} catch (UnsupportedParamTypeInConfigException $e) {
				throw new ShouldNotHappenException(sprintf('%s: %s', $this->formatter->formatIdentifier($namespaces), $e->getMessage()));
			}
		}
		return array_values($disallowedNamespaces);
	}

}
