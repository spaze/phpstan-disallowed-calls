<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedNamespace
{

	/** @var string */
	private $namespace;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var string|null */
	private $errorIdentifier;


	/**
	 * @param string $namespace
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param string|null $errorIdentifier
	 */
	public function __construct(string $namespace, ?string $message, array $allowIn, ?string $errorIdentifier)
	{
		$this->namespace = ltrim($namespace, '\\');
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->errorIdentifier = $errorIdentifier;
	}


	public function getNamespace(): string
	{
		return $this->namespace;
	}


	public function getMessage(): string
	{
		return $this->message ?? 'because reasons';
	}


	/**
	 * @return string[]
	 */
	public function getAllowIn(): array
	{
		return $this->allowIn;
	}


	public function getErrorIdentifier(): ?string
	{
		return $this->errorIdentifier;
	}

}
