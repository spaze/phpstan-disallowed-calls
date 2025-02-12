<?php
declare(strict_types = 1);

#[\Attribute10]
function withAttribute10(): void
{
	strtolower('');
	strtoupper('');
}


#[\Attribute11]
function withAttribute11(): void
{
	strtolower('');
	strtoupper('');
}


#[\Attribute10, \Attribute12]
function withAttribute10And12(): void
{
}


#[\Attribute11, \Attribute13]
function withAttribute11And13(): void
{
}
