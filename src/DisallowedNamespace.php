<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedNamespace implements Disallowed
{

	/** @var string */
	private $namespace;

	/** @var string|null */
	private $message;

	/** @var string[] */
	private $allowIn;

	/** @var string[] */
	private $allowExceptIn;

	/** @var string|null */
	private $errorIdentifier;

	/** @var string|null */
	private $errorTip;


	/**
	 * @param string $namespace
	 * @param string|null $message
	 * @param string[] $allowIn
	 * @param string[] $allowExceptIn
	 * @param string|null $errorIdentifier
	 * @param string|null $errorTip
	 */
	public function __construct(
		string $namespace,
		?string $message,
		array $allowIn,
		array $allowExceptIn,
		?string $errorIdentifier,
		?string $errorTip
	) {
		$this->namespace = ltrim($namespace, '\\');
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


	public function getMessage(): string
	{
		return $this->message ?? 'because reasons';
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


	public function getErrorIdentifier(): ?string
	{
		return $this->errorIdentifier;
	}


	public function getErrorTip(): ?string
	{
		return $this->errorTip;
	}

}
