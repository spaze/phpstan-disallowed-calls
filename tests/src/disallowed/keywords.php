<?php
declare(strict_types = 1);

// all disallowed

function useGlobal(): void {
	global $globalVar;
	Global $globalVar2;
}
