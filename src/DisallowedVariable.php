<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Exceptions\NotImplementedYetException;

class DisallowedVariable implements Disallowed
{

	/** @var string */
	private $variable;

	/** @var string|null */
	private $message;

	/** @var list<string> */
	private $allowIn;

	/** @var list<string> */
	private $allowExceptIn;

	/** @var string|null */
	private $errorIdentifier;

	/** @var string|null */
	private $errorTip;


	/**
	 * @param string $variable
	 * @param string|null $message
	 * @param list<string> $allowIn
	 * @param list<string> $allowExceptIn
	 * @param string|null $errorIdentifier
	 * @param string|null $errorTip
	 */
	public function __construct(
		string $variable,
		?string $message,
		array $allowIn,
		array $allowExceptIn,
		?string $errorIdentifier,
		?string $errorTip
	) {
		$this->variable = $variable;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowExceptIn = $allowExceptIn;
		$this->errorIdentifier = $errorIdentifier;
		$this->errorTip = $errorTip;
	}


	public function getVariable(): string
	{
		return $this->variable;
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


	public function getErrorIdentifier(): ?string
	{
		return $this->errorIdentifier;
	}


	public function getErrorTip(): ?string
	{
		return $this->errorTip;
	}

}
