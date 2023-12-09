<?php
declare(strict_types = 1);

namespace PhpOption;

use ArrayAccess;
use EmptyIterator;
use IteratorAggregate;
use ArrayIterator;
use Traversable;

/**
 * @template T
 *
 * @implements IteratorAggregate<T>
 */
abstract class Option implements IteratorAggregate
{
	public const NAME = 'Option';

	/**
	 * Creates an option from an array's value.
	 *
	 * If the key does not exist in the array, the array is not actually an
	 * array, or the array's value at the given key is null, None is returned.
	 * Otherwise, Some is returned wrapping the value at the given key.
	 *
	 * @template S
	 *
	 * @param array<string|int,S>|ArrayAccess<string|int,S>|null $array A potential array or \ArrayAccess value.
	 * @param string                                             $key   The key to check.
	 *
	 * @return Option<S>
	 */
	public static function fromArraysValue($array, $key)
	{
		if (!(is_array($array) || $array instanceof ArrayAccess) || !isset($array[$key])) {
			return None::create();
		}

		return new Some($array[$key]);
	}
}


/**
 * @extends Option<mixed>
 */
final class None extends Option
{
	/** @var None|null */
	private static $instance;

	/**
	 * @return None
	 */
	public static function create()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function getIterator(): Traversable
	{
		return new EmptyIterator();
	}
}




/**
 * @template T
 *
 * @extends  Option<T>
 */
final class Some extends Option
{
	/** @var T */
	private $value;


	/**
	 * @param T $value
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}


	/**
	 * @template U
	 *
	 * @param U $value
	 *
	 * @return Some<U>
	 */
	public static function create($value)
	{
		return new self($value);
	}


	public function getIterator(): Traversable
	{
		return new ArrayIterator([$this->value]);
	}
}
