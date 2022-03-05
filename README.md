# Laravel Threema Notification Channel

Laravel package for sending notifications with Threema.

## Prerequisites

1. Add the following repository to your `composer.json`:

```json
{
	"repositories": [
		{
			"type": "package",
			"package": {
				"name": "threema/msgapi-sdk",
				"version": "1.5.6",
				"dist": {
					"url": "https://gateway.threema.ch/sdk/threema-msgapi-sdk-php-1.5.6.zip",
					"type": "zip"
				}
			}
		}
	]
}
```

2. Install the package with Composer:

```shell
composer require ablota/laravel-threema-notification-channel
```

3. Request a [Threema Gateway ID](https://gateway.threema.ch/en/products). Register to get a basic ID for testing immediately. If you are interested in an end-to-end ID, contact [Threema](https://gateway.threema.ch/en/contact) and they will usually provide you with an ID for testing.

## Configuration

The package includes a [configuration file](config/threema.php). However, you are not required to export this configuration file to your own
application. You can simply use the environment variables below:

```shell
// Required
THREEMA_GATEWAY_ID=
THREEMA_GATEWAY_SECRET=

// Optional
THREEMA_GATEWAY_PRIVATE_KEY=
THREEMA_MSGAPI_HOST=
THREEMA_TLS_FORCE_HTTPS=true|false
THREEMA_TLS_CIPHER=...
THREEMA_TLS_VERSION=1.0|1.1|1.2
```

The required variables are shown on the [Threema Gateway](https://gateway.threema.ch/en/id) website. The `THREEMA_GATEWAY_PRIVATE_KEY` is required in
hex if you're using the end-to-end mode.

## Formatting Notifications

If a notification supports being sent as a Threema message, you should define a `toThreema` method on the notification class. This method will receive
a `$notifiable` entity and should return an `Illuminate\Notifications\Messages\ThreemaMessage` instance. Let's take a look at a basic `toThreema`
example:

```php
use Illuminate\Notifications\Messages\ThreemaMessage;
use Illuminate\Notifications\Messages\ThreemaTextMessage;

public function toThreema(mixed $notifiable): ThreemaMessage
{
	return new ThreemaTextMessage('Hello World!');
}
```

_Right now only text messages are supported. More message types are coming soon._

## Routing Notifications

To route Threema notifications to the proper Threema ID, define a `routeNotificationForThreema` method on your notifiable entity:

```php
use Threema\MsgApi\Receiver;

public function routeNotificationForThreema(mixed $notification): Receiver
{
	return new Receiver($this->threema_id, Receiver::TYPE_ID);
}
```

By using `Receiver::TYPE_EMAIL` or `Receiver::TYPE_PHONE` you can make use of an automatic ID lookup.
