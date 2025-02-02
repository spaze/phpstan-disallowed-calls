<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Exceptions\NotImplementedYetException;

class DisallowedConstant implements Disallowed
{

	private string $constant;

	private ?string $message;

	/** @var list<string> */
	private array $allowIn;

	/** @var list<string> */
	private array $allowExceptIn;

	private ?string $errorIdentifier;

	private ?string $errorTip;


	/**
	 * @param string $constant
	 * @param string|null $message
	 * @param list<string> $allowIn
	 * @param list<string> $allowExceptIn
	 * @param string|null $errorIdentifier
	 * @param string|null $errorTip
	 */
	public function __construct(
		string $constant,
		?string $message,
		array $allowIn,
		array $allowExceptIn,
		?string $errorIdentifier,
		?string $errorTip
	) {
		$this->constant = $constant;
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->allowExceptIn = $allowExceptIn;
		$this->errorIdentifier = $errorIdentifier;
		$this->errorTip = $errorTip;
	}


	public function getConstant(): string
	{
		return $this->constant;
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


	public function getAllowInCallsWithAttributes(): array
	{
		throw new NotImplementedYetException();
	}


	public function getAllowExceptInCallsWithAttributes(): array
	{
		throw new NotImplementedYetException();
	}


	public function getAllowInClassWithMethodAttributes(): array
	{
		throw new NotImplementedYetException();
	}


	public function getAllowExceptInClassWithMethodAttributes(): array
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
