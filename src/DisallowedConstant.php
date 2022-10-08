<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedConstant
{

	/** @var string */
	private $constant;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var string|null */
	private $errorIdentifier;

	/** @var string|null */
	private $errorTip;


	/**
	 * DisallowedCall constructor.
	 *
	 * @param string $constant
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param string|null $errorIdentifier
	 * @param string|null $errorTip
	 */
	public function __construct(
		string $constant,
		?string $message,
		array $allowIn,
		?string $errorIdentifier,
		?string $errorTip
	) {
		$this->constant = ltrim($constant, '\\');
		$this->message = $message;
		$this->allowIn = $allowIn;
		$this->errorIdentifier = $errorIdentifier;
		$this->errorTip = $errorTip;
	}


	public function getConstant(): string
	{
		return $this->constant;
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


	public function getErrorTip(): ?string
	{
		return $this->errorTip;
	}

}
