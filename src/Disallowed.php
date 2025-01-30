<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

interface Disallowed
{

	/**
	 * @return list<string>
	 */
	public function getAllowIn(): array;


	/**
	 * @return list<string>
	 */
	public function getAllowExceptIn(): array;


	/**
	 * @return list<string>
	 */
	public function getAllowInCalls(): array;


	/**
	 * @return list<string>
	 */
	public function getAllowExceptInCalls(): array;


	/**
	 * @return list<string>
	 */
	public function getAllowInClassWithAttributes(): array;


	/**
	 * @return list<string>
	 */
	public function getAllowExceptInClassWithAttributes(): array;


	/**
	 * @return list<string>
	 */
	public function getAllowInClassWithMethodAttributes(): array;


	/**
	 * @return list<string>
	 */
	public function getAllowExceptInClassWithMethodAttributes(): array;

}
