<?php
declare(strict_types = 1);

apache_setenv('foo', 'bar');
dl('ul.dll');
eval('echo 1337;');
extract([]);
posix_getpwuid(1);
posix_kill(1337, 303);
posix_mkfifo('foo', 0666);
posix_mknod('nod32', 0);
highlight_file(__FILE__);
show_source(__FILE__);
pfsockopen('localhost');
print_r([]);
print_r([1], true);
print_r([2], false);
proc_nice(808);
putenv('FOO=WUT');
socket_create_listen(1024);
socket_listen(socket_create(303, 808, 909));
var_dump([]);
var_export([]);
var_export([1], true);
var_export([2], false);
