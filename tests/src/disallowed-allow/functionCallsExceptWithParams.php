<?php
declare(strict_types = 1);

// in the except zone: allowed only when first param matches allowParamsInAllowed
pow(2, 3);
pow(3, 2);

// in the except zone: allowed only when first param doesn't match allowExceptParamsInAllowed
intdiv(3, 2);
intdiv(2, 3);
