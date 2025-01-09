<?php
declare(strict_types = 1);

function foo(callable $callback): void { $callback(); }

class Callbacks
{
	public function call(): void { echo __METHOD__; }
	public static function staticCall(): void { echo __METHOD__; }
}

class CallableParams
{
	public function call(callable $callback, ?int $num = null): void {}
	public static function staticCall(callable $callback, ?int $num = null): void {}
}

class Callbacks2 extends Callbacks {}
class CallableParams2 extends CallableParams {}

// disallowed function callback params in function calls
$varDump = 'var_dump';
array_map('var_dump', []);
array_map($varDump, []);
array_map(callback: 'var_dump', array: []);
array_map(array: [], callback: 'var_dump');

foo('var_dump');
foo($varDump);

// not a callback param
strlen('var_dump');
strlen($varDump);

// disallowed method callback params in function calls
$call = 'call';
$staticCall = 'staticCall';
$callbacks = new Callbacks();
$callbacks2 = new Callbacks2();
array_map([$callbacks, $call], []);
foo([$callbacks, 'call']);
foo([$callbacks, $call]);
foo([new Callbacks, $call]);

foo([$callbacks2, 'call']);
foo([$callbacks2, $call]);
foo([new Callbacks2, $call]);

foo(['Callbacks', 'staticCall']);
foo(['Callbacks', $staticCall]);
foo([Callbacks::class, 'staticCall']);
foo([Callbacks::class, $staticCall]);

foo(['Callbacks2', 'staticCall']);
foo(['Callbacks2', $staticCall]);
foo([Callbacks2::class, 'staticCall']);
foo([Callbacks2::class, $staticCall]);

$callableParams = new CallableParams();
$callableParams2 = new CallableParams2();

// disallowed function callback params in method calls
$callableParams->call('var_dump');
$callableParams->call($varDump);
$callableParams2->call('var_dump');
$callableParams2->call($varDump);

// disallowed method callback params in method calls
$callableParams->call([$callbacks, 'call']);
$callableParams->call([$callbacks, $call]);
$callableParams->call([new Callbacks, $call]);
$callableParams->call([$callbacks2, 'call']);
$callableParams->call([$callbacks2, $call]);
$callableParams->call([new Callbacks2, $call]);
$callableParams->call(['Callbacks', 'staticCall']);
$callableParams->call(['Callbacks', $staticCall]);
$callableParams->call([Callbacks::class, 'staticCall']);
$callableParams->call([Callbacks::class, $staticCall]);
$callableParams->call(['Callbacks2', 'staticCall']);
$callableParams->call(['Callbacks2', $staticCall]);
$callableParams->call([Callbacks2::class, 'staticCall']);
$callableParams->call([Callbacks2::class, $staticCall]);

$callableParams2->call([$callbacks, 'call']);
$callableParams2->call([$callbacks, $call]);
$callableParams2->call([new Callbacks, $call]);
$callableParams2->call([$callbacks2, 'call']);
$callableParams2->call([$callbacks2, $call]);
$callableParams2->call([new Callbacks2, $call]);
$callableParams2->call(['Callbacks', 'staticCall']);
$callableParams2->call(['Callbacks', $staticCall]);
$callableParams2->call([Callbacks::class, 'staticCall']);
$callableParams2->call([Callbacks::class, $staticCall]);
$callableParams2->call(['Callbacks2', 'staticCall']);
$callableParams2->call(['Callbacks2', $staticCall]);
$callableParams2->call([Callbacks2::class, 'staticCall']);
$callableParams2->call([Callbacks2::class, $staticCall]);

IntlChar::enumCharTypes($varDump);
CallableParams::staticCall('var_dump');
CallableParams::staticCall($varDump);
CallableParams::staticCall([$callbacks, 'call']);
CallableParams::staticCall([$callbacks, $call]);
CallableParams::staticCall([new Callbacks, $call]);
CallableParams::staticCall([$callbacks2, 'call']);
CallableParams::staticCall([$callbacks2, $call]);
CallableParams::staticCall([new Callbacks2, $call]);
CallableParams::staticCall(['Callbacks', 'staticCall']);
CallableParams::staticCall(['Callbacks', $staticCall]);
CallableParams::staticCall([Callbacks::class, 'staticCall']);
CallableParams::staticCall([Callbacks::class, $staticCall]);
CallableParams::staticCall(['Callbacks2', 'staticCall']);
CallableParams::staticCall(['Callbacks2', $staticCall]);
CallableParams::staticCall([Callbacks2::class, 'staticCall']);
CallableParams::staticCall([Callbacks2::class, $staticCall]);

CallableParams2::staticCall('var_dump');
CallableParams2::staticCall($varDump);
CallableParams2::staticCall([$callbacks, 'call']);
CallableParams2::staticCall([$callbacks, $call]);
CallableParams2::staticCall([new Callbacks, $call]);
CallableParams2::staticCall([$callbacks2, 'call']);
CallableParams2::staticCall([$callbacks2, $call]);
CallableParams2::staticCall([new Callbacks2, $call]);
CallableParams2::staticCall(['Callbacks', 'staticCall']);
CallableParams2::staticCall(['Callbacks', $staticCall]);
CallableParams2::staticCall([Callbacks::class, 'staticCall']);
CallableParams2::staticCall([Callbacks::class, $staticCall]);
CallableParams2::staticCall(['Callbacks2', 'staticCall']);
CallableParams2::staticCall(['Callbacks2', $staticCall]);
CallableParams2::staticCall([Callbacks2::class, 'staticCall']);
CallableParams2::staticCall([Callbacks2::class, $staticCall]);

interface CallbacksInterface
{
	public function interfaceCall(): void;
	public static function interfaceStaticCall(): void;
}

trait CallbacksTrait
{
	public function traitCall(): void { echo __METHOD__; }
	public static function traitStaticCall(): void { echo __METHOD__; }
}

class CallbacksPlusPlus implements CallbacksInterface
{
	use CallbacksTrait;

	public function interfaceCall(): void { echo __METHOD__; }
	public static function interfaceStaticCall(): void { echo __METHOD__; }
}

$callbacksPlusPlus = new CallbacksPlusPlus();
foo([$callbacksPlusPlus, 'interfaceCall']);
foo([$callbacksPlusPlus, 'interfaceStaticCall']);
foo([$callbacksPlusPlus, 'traitCall']);
foo([CallbacksPlusPlus::class, 'traitStaticCall']);

$callableParams->call([$callbacksPlusPlus, 'interfaceCall']);
$callableParams->call([$callbacksPlusPlus, 'interfaceStaticCall']);
$callableParams->call([$callbacksPlusPlus, 'traitCall']);
$callableParams->call([CallbacksPlusPlus::class, 'traitStaticCall']);

CallableParams::staticCall([$callbacksPlusPlus, 'interfaceCall']);
CallableParams::staticCall([$callbacksPlusPlus, 'interfaceStaticCall']);
CallableParams::staticCall([$callbacksPlusPlus, 'traitCall']);
CallableParams::staticCall([CallbacksPlusPlus::class, 'traitStaticCall']);
