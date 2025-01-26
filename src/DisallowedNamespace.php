<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Exceptions\NotImplementedYetException;

class DisallowedNamespace implements Disallowed
{

	private string $namespace;

	/** @var list<string> */
	private array $excludes;

	private ?string $message;

	/** @var list<string> */
	private array $allowIn;

	/** @var list<string> */
	private array $allowExceptIn;

	private ?string $errorIdentifier;

	private ?string $errorTip;


	/**
	 * @param string $namespace
	 * @param list<string> $excludes
	 * @param string|null $message
	 * @param list<string> $allowIn
	 * @param list<string> $allowExceptIn
	 * @param string|null $errorIdentifier
	 * @param string|null $errorTip
	 */
	public function __construct(
		string $namespace,
		array $excludes,
		?string $message,
		array $allowIn,
		array $allowExceptIn,
		?string $errorIdentifier,
		?string $errorTip
	) {
		$this->namespace = $namespace;
		$this->excludes = $excludes;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowExceptIn = $allowExceptIn;
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


	public function getMessage(): ?string
	{
		return $this->message;
	}


	/** @inheritDoc */
	public function getAllowIn(): array
	{
		return $this->allowIn;
	}


	/** @inheritDoc */
	public function getAllowExceptIn(): array
	{
		return $this->allowExceptIn;
	}


	public function getAllowInCalls(): array
	{
		throw new NotImplementedYetException();
	}


	public function getAllowExceptInCalls(): array
	{
		throw new NotImplementedYetException();
	}


	public function getAllowInClassWithAttributes(): array
	{
		throw new NotImplementedYetException();
	}


	public function getAllowExceptInClassWithAttributes(): array
	{
		throw new NotImplementedYetException();
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
