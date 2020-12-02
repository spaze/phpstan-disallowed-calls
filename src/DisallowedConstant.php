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


	/**
	 * DisallowedCall constructor.
	 *
	 * @param string $constant
	 * @param string|null $message
	 * @param string[] $allowIn
	 */
	public function __construct(string $constant, ?string $message, array $allowIn)
	{
		$this->constant = ltrim($constant, '\\');
		$this->message = $message;
		$this->allowIn = $allowIn;
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

}
