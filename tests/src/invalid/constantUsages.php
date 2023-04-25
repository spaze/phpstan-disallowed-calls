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

// a valid type, for a change
/** @var class-string<DateTimeZone> $tz */
$tz = DateTimeZone::class;
$tz::UTC;

// this constant doesn't exist
/** @var class-string<DateTimeZone> $tz */
$tz = DateTimeZone::class;
$tz::FTC;
