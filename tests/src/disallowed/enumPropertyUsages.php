<?php
declare(strict_types = 1);

// disallowed enum property
$enum = Enums\BackedEnum::Fred;
echo $enum->value;
