<?php
declare(strict_types = 1);

in_array('foo', []);
in_array('foo', [], true);
in_array('foo', [], false);
htmlspecialchars('foo');
htmlspecialchars('foo', ENT_QUOTES);
htmlspecialchars('foo', 3);
htmlspecialchars('foo', ENT_QUOTES | ENT_HTML5);
htmlspecialchars('foo', 51); // ENT_QUOTES | ENT_HTML5
htmlspecialchars('foo', ENT_XHTML);
htmlspecialchars('foo', 4);
