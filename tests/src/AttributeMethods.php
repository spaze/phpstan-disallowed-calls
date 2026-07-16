<?php
declare(strict_types = 1);

namespace AttributeMethods;

use Attribute;

#[Attribute]
class SomeAttribute
{
}


#[Attribute]
class AnotherAttribute
{
}


#[Attribute]
class FuncAttribute
{
}


#[SomeAttribute]
class Presenter
{

	#[SomeAttribute]
	public function actionFoo(): void
	{
	}


	#[SomeAttribute]
	public function renderBar(): void
	{
	}


	#[SomeAttribute]
	public string $actionProperty = '';


	public function actionWithNestedFunction(): void
	{
		#[SomeAttribute]
		function nestedInAction(): void
		{
		}
	}


	public function renderWithNestedFunction(): void
	{
		#[SomeAttribute]
		function nestedInRender(): void
		{
		}
	}

}


class Service
{

	#[AnotherAttribute]
	public function forbiddenMethod(): void
	{
	}


	#[AnotherAttribute]
	public function allowedMethod(): void
	{
	}

}


#[AnotherAttribute]
class ServiceWithClassAttribute
{
}


#[FuncAttribute]
function allowedFunction(): void
{
}


#[FuncAttribute]
function otherFunction(): void
{
}
