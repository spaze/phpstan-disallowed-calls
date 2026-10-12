<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\UsageFactory;

use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;
use Spaze\PHPStan\Rules\Disallowed\UsageFactory\NamespaceUsage;

class NamespaceUsageFactory
{

	private Normalizer $normalizer;


	public function __construct(Normalizer $normalizer)
	{
		$this->normalizer = $normalizer;
	}


	public function create(string $namespace, string $description, string $identifier, bool $isUseItem = false): NamespaceUsage
	{
		return new NamespaceUsage($this->normalizer->normalizeNamespace($namespace), $description, $identifier, $isUseItem);
	}


	public function createClassUsage(FullyQualified $name, Scope $scope): NamespaceUsage
	{
		$classReflection = $scope->resolveTypeByName($name)->getClassReflection();
		if ($classReflection !== null) {
			if ($classReflection->isEnum()) {
				return $this->create($name->toString(), 'Enum', ErrorIdentifiers::DISALLOWED_ENUM);
			} elseif ($classReflection->isInterface()) {
				return $this->create($name->toString(), 'Interface', ErrorIdentifiers::DISALLOWED_INTERFACE);
			} elseif ($classReflection->isTrait()) {
				return $this->create($name->toString(), 'Trait', ErrorIdentifiers::DISALLOWED_TRAIT);
			}
		}
		return $this->create($name->toString(), 'Class', ErrorIdentifiers::DISALLOWED_CLASS);
	}

}
