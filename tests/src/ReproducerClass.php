<?php
declare(strict_types = 1);

namespace Reproducer;

use Attribute;

#[Attribute]
class AsCommandHandler {}

class EventRecorder {}

class ReproducerClassNotAllowed
{
	public function __invoke(EventRecorder $eventRecorder): void
	{
	}
}
class ReproducerClassWithConstructorNotAllowed
{
	public function __construct()
	{
	}

	public function __invoke(EventRecorder $eventRecorder): void
	{
	}
}

class ReproducerClassAllowed
{
	#[AsCommandHandler]
	public function __invoke(EventRecorder $eventRecorder): void
	{
	}
}

class ReproducerClassWithConstructorAllowed
{
	public function __construct()
	{
	}

	#[AsCommandHandler]
	public function __invoke(EventRecorder $eventRecorder): void
	{
	}
}
