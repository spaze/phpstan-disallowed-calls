<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\UsageFactory;

class NamespaceUsage
{

	private string $namespace;

	private string $description;

	private string $identifier;

	private bool $isUseItem;


	public function __construct(string $namespace, string $description, string $identifier, bool $isUseItem = false)
	{
		$this->namespace = $namespace;
		$this->description = $description;
		$this->identifier = $identifier;
		$this->isUseItem = $isUseItem;
	}


	public function getNamespace(): string
	{
		return $this->namespace;
	}


	public function getDescription(): string
	{
		return $this->description;
	}


	public function getIdentifier(): string
	{
		return $this->identifier;
	}


	public function isUseItem(): bool
	{
		return $this->isUseItem;
	}

}
