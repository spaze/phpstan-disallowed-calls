<?php
declare(strict_types = 1);

in_array('foo', []);
in_array('foo', [], true);
in_array('foo', [], false);
htmlspecialchars('foo');
htmlspecialchars('foo', ENT_QUOTES);
htmlspecialchars('foo', 3);
htmlspecialchars('foo', 4);
