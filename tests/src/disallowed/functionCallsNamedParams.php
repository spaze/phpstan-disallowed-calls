<?php
declare(strict_types = 1);

// second/$value param needed
setcookie('foo');
setcookie('foo', 'bar');
setcookie('foo', value: 'bar');
setcookie('foo', 'bar', 0, '/');
setcookie('foo', 'bar', path: '/');
