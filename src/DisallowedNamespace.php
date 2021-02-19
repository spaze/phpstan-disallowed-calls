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


	/**
	 * @param string $namespace
	 * @param string|null $message
	 * @param string[] $allowIn
	 */
	public function __construct(string $namespace, ?string $message, array $allowIn)
	{
		$this->namespace = ltrim($namespace, '\\');
		$this->message = $message;
		$this->allowIn = $allowIn;
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

}
