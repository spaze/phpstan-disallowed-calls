<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedSuperglobal
{

	/** @var string */
	private $superglobal;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var string */
	private $errorIdentifier;


	/**
	 * @param string $superglobal
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param string $errorIdentifier
	 */
	public function __construct(string $superglobal, ?string $message, array $allowIn, string $errorIdentifier)
	{
		$this->superglobal = $superglobal;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->errorIdentifier = $errorIdentifier;
	}


	public function getSuperglobal(): string
	{
		return $this->superglobal;
	}


	public function getMessage(): ?string
	{
		return $this->message;
	}


	/**
	 * @return string[]
	 */
	public function getAllowIn(): array
	{
		return $this->allowIn;
	}


	public function getErrorIdentifier(): string
	{
		return $this->errorIdentifier;
	}

}
