<?php
declare(strict_types = 1);

namespace Attributes;

#[AttributeClass]
enum EnumWithAttributes
{

	#[AttributeClass]
	public const ENUM_CONST = true;

	#[AttributeClass]
	case Foo;

}


#[AttributeClass]
trait TraitWithAttributes
{

	#[AttributeClass]
	private const TRAIT_CONST = true;

	#[AttributeClass]
	private $bar;


	#[AttributeClass]
	public function traitMethod(
		#[AttributeClass]
		bool $param
	): void {
	}

}

// https://phpstan.org/blog/how-phpstan-analyses-traits
class ClassWithTraitWithAttributes
{

	use TraitWithAttributes;

}


#[AttributeClass]
interface InterfaceWithAttributes
{

	#[AttributeClass]
	public function interfaceMethod(
		#[AttributeClass]
		bool $param
	): void;

}


#[AttributeClass]
function functionWithAttributes(
	#[AttributeClass]
	int $param
): void {
}


$anonymousFunction = #[AttributeClass] function (
	#[AttributeClass]
	int $param
): void {
};


$arrowFunction = #[AttributeClass] fn(
	#[AttributeClass]
	int $param
) => 1;
