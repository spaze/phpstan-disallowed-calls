<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedVariable
{

	/** @var string */
	private $variable;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var string */
	private $errorIdentifier;


	/**
	 * @param string $variable
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param string $errorIdentifier
	 */
	public function __construct(string $variable, ?string $message, array $allowIn, string $errorIdentifier)
	{
		$this->variable = $variable;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->errorIdentifier = $errorIdentifier;
	}


	public function getVariable(): string
	{
		return $this->variable;
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
