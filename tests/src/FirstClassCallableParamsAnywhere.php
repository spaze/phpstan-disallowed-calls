<?php
declare(strict_types = 1);

$fn = crc32(...); // disallowed: allowParamsAnywhere, null args can't satisfy param condition
$fn = strtolower(...); // allowed: allowExceptParamsAnywhere, null args don't trigger disallow condition
