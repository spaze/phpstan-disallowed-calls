<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedConfig;

class DisallowedNamespace implements Disallowed
{

	private string $namespace;

	/** @var list<string> */
	private array $excludes;

	/** @var list<string> */
	private array $excludeWithAttributes;

	private ?string $message;

	private ?string $errorIdentifier;

	private ?string $errorTip;

	private AllowedConfig $allowedConfig;

	private bool $allowInUse;


	/**
	 * @param string $namespace
	 * @param list<string> $excludes
	 * @param list<string> $excludeWithAttributes
	 * @param string|null $message
	 * @param AllowedConfig $allowedConfig
	 * @param string|null $errorIdentifier
	 * @param string|null $errorTip
	 */
	public function __construct(
		string $namespace,
		array $excludes,
		array $excludeWithAttributes,
		?string $message,
		AllowedConfig $allowedConfig,
		bool $allowInUse,
		?string $errorIdentifier,
		?string $errorTip
	) {
		$this->namespace = $namespace;
		$this->excludes = $excludes;
		$this->excludeWithAttributes = $excludeWithAttributes;
		$this->message = $message;
		$this->allowedConfig = $allowedConfig;
		$this->allowInUse = $allowInUse;
		$this->errorIdentifier = $errorIdentifier;
		$this->errorTip = $errorTip;
	}


	public function getNamespace(): string
	{
		return $this->namespace;
	}


	/**
	 * @return list<string>
	 */
	public function getExcludes(): array
	{
		return $this->excludes;
	}


	/**
	 * @return list<string>
	 */
	public function getExcludeWithAttributes(): array
	{
		return $this->excludeWithAttributes;
	}


	public function getMessage(): ?string
	{
		return $this->message;
	}


	/** @inheritDoc */
	public function getAllowIn(): array
	{
		return $this->allowedConfig->getAllowIn();
	}


	/** @inheritDoc */
	public function getAllowExceptIn(): array
	{
		return $this->allowedConfig->getAllowExceptIn();
	}


	public function getAllowInCalls(): array
	{
		return $this->allowedConfig->getAllowInCalls();
	}


	public function getAllowInInstanceOf(): array
	{
		return $this->allowedConfig->getAllowInInstanceOf();
	}


	public function getAllowExceptInInstanceOf(): array
	{
		return $this->allowedConfig->getAllowExceptInInstancesOf();
	}


	public function getAllowExceptInCalls(): array
	{
		return $this->allowedConfig->getAllowExceptInCalls();
	}


	public function getAllowInClassWithAttributes(): array
	{
		return $this->allowedConfig->getAllowInClassWithAttributes();
	}


	public function getAllowExceptInClassWithAttributes(): array
	{
		return $this->allowedConfig->getAllowExceptInClassWithAttributes();
	}


	public function getAllowInCallsWithAttributes(): array
	{
		return $this->allowedConfig->getAllowInCallsWithAttributes();
	}


	public function getAllowExceptInCallsWithAttributes(): array
	{
		return $this->allowedConfig->getAllowExceptInCallsWithAttributes();
	}


	public function getAllowInClassWithMethodAttributes(): array
	{
		return $this->allowedConfig->getAllowInClassWithMethodAttributes();
	}


	public function getAllowExceptInClassWithMethodAttributes(): array
	{
		return $this->allowedConfig->getAllowExceptInClassWithMethodAttributes();
	}


	public function isAllowInUse(): bool
	{
		return $this->allowInUse;
	}


	public function getErrorIdentifier(): ?string
	{
		return $this->errorIdentifier;
	}


	public function getErrorTip(): ?string
	{
		return $this->errorTip;
	}

}
