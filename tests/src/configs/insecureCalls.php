<?php
declare(strict_types = 1);

md5('foo');
sha1();
md5_file();
sha1_file();
hash('md5');
hash('sha1');
hash('sha256');
hash_file('md5');
hash_file('sha1');
hash_file('sha256');
hash_init('md5');
hash_init('sha1');
hash_init('sha256');
mysql_query();
mysql_unbuffered_query();
mysqli_query();
mysqli_multi_query();
mysqli_real_query();

(new mysqli())->query('SELECT * FROM users WHERE id = 1 OR 1');
(new mysqli())->multi_query('SELECT * FROM users WHERE id = 2 OR 1');
(new mysqli())->real_query('SELECT * FROM users WHERE id = 3 OR 1');
