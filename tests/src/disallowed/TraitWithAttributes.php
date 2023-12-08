<?php
declare(strict_types = 1);

namespace DebuggingTraits;

#[AttributeClass1]
trait TraitWithAttributes
{

	#[AttributeClass2]
	private const TRAIT_CONST = true;

	#[AttributeClass3]
	private $bar;


	#[AttributeClass4]
	public function traitMethod(
		#[AttributeClass5]
		bool $param
	): void {
	}

}


class ClassWithTraitWithAttributes
{

	use TraitWithAttributes;

}
