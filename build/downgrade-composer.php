#!/usr/bin/env php
<?php
declare(strict_types = 1);

$composer = __DIR__ . '/../composer.json';
$json = json_decode(file_get_contents($composer));
$json->require->php = '^7.2 || ^8.0';
file_put_contents($composer, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
