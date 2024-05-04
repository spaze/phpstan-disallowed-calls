<?php
declare(strict_types = 1);

// all disallowed

$true = $false = $maybe = null;

if ($true) {
	echo 'true';
} elseif ($false) {
	echo 'false';
} else if ($maybe) {
	return 1;
} else {
	echo 'else';
}

If ($true):
	echo 'true';
ElseIf ($false):
	echo 'false';
Else:
	echo 'else';
EndIf;

while ($true) {
	echo 'true';
	if (0) {
		continue;
	}
	If (1) {
		break;
	}
}

While ($false):
	echo 'false';
	if (0) {
		Continue;
	}
	if (1) {
		Break;
	}
EndWhile;

Do {
	echo 'maybe';
	if (0) {
		Continue;
	}
	if (1) {
		Break;
	}
} While ($maybe);

for ($i = 0; $i < 10; $i++) {
	echo $i;
	if (0) {
		continue;
	}
	If (1) {
		break;
	}
}

For ($i = 0; $i < 10; $i++):
	echo $i;
	if (0) {
		Continue;
	}
	if (1) {
		Break;
	}
EndFor;

foreach ([] as $key => $value) {
	echo $value;
	if (0) {
		continue;
	}
	If (1) {
		break;
	}
}

ForEach ([] as $key => $value):
	echo $value;
	if (0) {
		Continue;
	}
	if (1) {
		Break;
	}
EndForEach;

switch ($i) {
	case 10:
		echo 0;
		break;
	default:
		break;
}

Switch ($i):
	Case 10:
		echo 0;
		Break;
	Default:
		Break;
EndSwitch;

match ($true) {
	null => 'null',
};

goto main_sub3;
declare(ticks = 123);
main_sub3:

require __FILE__;
include __FILE__;
require_once __FILE__;
include_once __FILE__;
Require __FILE__;
Include __FILE__;
Require_Once __FILE__;
Include_Once __FILE__;
