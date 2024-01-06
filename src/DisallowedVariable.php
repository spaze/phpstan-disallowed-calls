<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Spaze\PHPStan\Rules\Disallowed\Exceptions\NotImplementedYetException;

class DisallowedVariable implements Disallowed
{

	/**
	 * @param list<string> $allowIn
	 * @param list<string> $allowExceptIn
	 */
	public function __construct(
		private readonly string $variable,
		private readonly ?string $message,
		private readonly array $allowIn,
		private readonly array $allowExceptIn,
		private readonly ?string $errorIdentifier,
		private readonly ?string $errorTip,
	) {
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
