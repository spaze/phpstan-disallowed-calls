<?php
declare(strict_types = 1);

namespace ControlStructures;

class ControlStructures
{
}

class ChildControlStructures extends ControlStructures
{

	public function leMethod(): void
	{
		require __DIR__ . '/file';
		require_once __DIR__ . '/file';
		include __DIR__ . '/file';
		include_once __DIR__ . '/file';
	}

}

class ControlStructures2
{

	public function leMethod(): void
	{
		require __DIR__ . '/file';
		require_once __DIR__ . '/file';
		include __DIR__ . '/file';
		include_once __DIR__ . '/file';
	}

}
