<?php
declare(strict_types = 1);

// all allowed by path

function useGlobal(): void {
	global $globalVar;
	Global $globalVar2;
}
