<?php

use Threema\MsgApi\ConnectionSettings;

return [
	/*
	|--------------------------------------------------------------------------
	| Gateway ID
	|--------------------------------------------------------------------------
	|
	| The custom Gateway ID you requested on Threema Gateway.
	|
	*/
	'gateway_id' => env('THREEMA_GATEWAY_ID'),

	/*
	|--------------------------------------------------------------------------
	| Gateway Secret
	|--------------------------------------------------------------------------
	|
	| The secret of the custom Gateway ID you requested on Threema Gateway.
	|
	*/
	'gateway_secret' => env('THREEMA_GATEWAY_SECRET'),

	/*
	|--------------------------------------------------------------------------
	| Gateway Private Key
	|--------------------------------------------------------------------------
	|
	| The private key of the custom Gateway ID you requested on Threema Gateway.
	| (End-to-End)
	|
	*/
	'gateway_private_key' => env('THREEMA_GATEWAY_PRIVATE_KEY'),

	/*
	|--------------------------------------------------------------------------
	| MsgApi Host
	|--------------------------------------------------------------------------
	|
	| The host URL of the MsgApi server you want to send messages over.
	|
	*/
	'msgapi_host' => env('THREEMA_MSGAPI_HOST'),

	/*
	|--------------------------------------------------------------------------
	| TLS Options
	|--------------------------------------------------------------------------
	|
	| When connecting to the MsgApi server you can define the following TLS options.
	|
	*/
	'tls_options' => [
		ConnectionSettings::tlsOptionForceHttps => env('THREEMA_TLS_FORCE_HTTPS'),
		ConnectionSettings::tlsOptionCipher => env('THREEMA_TLS_CIPHER'),
		ConnectionSettings::tlsOptionVersion => env('THREEMA_TLS_VERSION'),
	],
];
