<?php
declare(strict_types = 1);

exec('whoami');
passthru('uname');
$pipes = [];
proc_open('pwd', [], $pipes);
shell_exec('cd');
`ls`;
system('ping');
pcntl_exec('man ls');
popen('cat /etc/passwd', 'r');
