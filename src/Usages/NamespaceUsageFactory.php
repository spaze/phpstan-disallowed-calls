<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class NamespaceUsageFactory
{

	private Normalizer $normalizer;


	public function __construct(Normalizer $normalizer)
	{
		$this->normalizer = $normalizer;
	}


	public function create(string $namespace, bool $isUseItem = false): NamespaceUsage
	{
		return new NamespaceUsage($this->normalizer->normalizeNamespace($namespace), $isUseItem);
	}

}
