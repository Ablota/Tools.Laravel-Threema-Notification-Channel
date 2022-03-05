<?php

namespace Illuminate\Notifications;

use Illuminate\Notifications\Channels\ThreemaChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;
use Threema\MsgApi\PublicKeyStores\File;

class ThreemaChannelServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/threema.php', 'threema');

		$this->app->singleton(ThreemaChannel::class, function ($app) {
			Storage::disk('local')->put('threema.pks', '');

			$config = $app['config']['threema'];
			$settings = new ConnectionSettings(
				$config['gateway_id'],
				$config['gateway_secret'],
				$config['msgapi_host'],
				$config['tls_options']
			);
			$publicKeyStore = new File(Storage::disk('local')->path('threema.pks'));
			$connection = new Connection($settings, $publicKeyStore);

			return new ThreemaChannel($connection, $config['gateway_private_key']);
		});

		Notification::resolved(function (ChannelManager $service) {
			$service->extend('threema', function ($app) {
				return $app->make(ThreemaChannel::class);
			});
		});
	}

	public function boot(): void
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/threema.php' => $this->app->configPath('threema.php'),
			], 'threema');
		}
	}
}
