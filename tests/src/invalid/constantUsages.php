<?php
declare(strict_types = 1);

// invalid type
/** @var string $cBeams */
$cBeams::GLITTER;

/** @var string $monster */
$monster = DateTime::class;
$monster::COOKIE;

/** @var class-string $monster */
$monster = DateTime::class;
$monster::COOKIE;

/** @var class-string<DateTimeZone> $tz */
$tz = DateTimeZone::class;
$tz::UTC;
