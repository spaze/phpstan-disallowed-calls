<?php
declare(strict_types = 1);

// disallowed constants with class wildcard
echo DateTime::ISO8601;
echo DateTimeImmutable::ISO8601;
echo DateTimeInterface::ISO8601;

// disallowed class constants with wildcard in constant
echo DateTimeInterface::RFC1123;
echo DateTimeInterface::RFC3339;
