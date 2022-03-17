<?php

namespace Illuminate\Notifications\Tests\Unit\Channels;

use Illuminate\Notifications\Channels\ThreemaChannel;
use Illuminate\Notifications\Exceptions\ThreemaChannelException;
use Illuminate\Notifications\Exceptions\ThreemaChannelResultException;
use Illuminate\Notifications\Messages\ThreemaAudioMessage;
use Illuminate\Notifications\Messages\ThreemaFileMessage;
use Illuminate\Notifications\Messages\ThreemaImageMessage;
use Illuminate\Notifications\Messages\ThreemaLocationMessage;
use Illuminate\Notifications\Messages\ThreemaMessage;
use Illuminate\Notifications\Messages\ThreemaTextMessage;
use Illuminate\Notifications\Messages\ThreemaVideoMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use PHPUnit\Framework\TestCase;
use Threema\MsgApi\Commands\Results\CapabilityResult;
use Threema\MsgApi\Commands\Results\FetchPublicKeyResult;
use Threema\MsgApi\Commands\Results\LookupIdResult;
use Threema\MsgApi\Commands\Results\SendE2EResult;
use Threema\MsgApi\Commands\Results\SendSimpleResult;
use Threema\MsgApi\Commands\Results\UploadFileResult;
use Threema\MsgApi\Connection;
use Threema\MsgApi\Core\Exception;
use Threema\MsgApi\Receiver;

class ThreemaChannelTest extends TestCase
{
	public const PUBLIC_KEY = '51a50031b2e203368b636f58a8a3aa36373d88888a6cbe6218f4d87614c23067';
	public const PRIVATE_KEY = 'ebb1ac251ae06b8ea15d9e91c346b0b90484002a44dd1331f92ab1331498a2a0';
	private Connection $connectionMock;

	/**
	 * @throws ThreemaChannelException
	 */
	public function testValidPrivateKey()
	{
		new ThreemaChannel($this->connectionMock, null);
		new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);

		$this->expectNotToPerformAssertions();
	}

	public function testInvalidPrivateKey()
	{
		$this->expectException(ThreemaChannelException::class);

		new ThreemaChannel($this->connectionMock, '0Z');
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageSimpleId()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendSimple')
			->willReturn(new SendSimpleResult(200, null));

		$channel = new ThreemaChannel($this->connectionMock);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageSimpleEmail()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelEmailNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendSimple')
			->willReturn(new SendSimpleResult(200, null));

		$channel = new ThreemaChannel($this->connectionMock);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageSimplePhone()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelPhoneNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendSimple')
			->willReturn(new SendSimpleResult(200, null));

		$channel = new ThreemaChannel($this->connectionMock);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageSimpleCustom()
	{
		$customConnectionMock = $this->createMock(Connection::class);
		$customConnectionMock
			->expects($this->once())
			->method('sendSimple')
			->willReturn(new SendSimpleResult(200, null));

		$customChannel = new ThreemaChannel($customConnectionMock);

		$notification = new ThreemaChannelTextMessageCustomNotificationTest($customChannel);
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->never())
			->method('sendSimple');

		$channel = new ThreemaChannel($this->connectionMock);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2EId()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2EEmail()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelEmailNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$this->connectionMock
			->expects($this->once())
			->method('keyLookupByEmail')
			->willReturn(new LookupIdResult(200, 'ECHOECHO'));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2EPhone()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelPhoneNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$this->connectionMock
			->expects($this->once())
			->method('keyLookupByPhoneNumber')
			->willReturn(new LookupIdResult(200, 'ECHOECHO'));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2EInvalidNotifiable()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelInvalidNotifiableTest();

		$this->expectException(ThreemaChannelException::class);

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2EInvalidLookup()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelEmailNotifiableTest();
		$lookupResult = new LookupIdResult(404, null);

		$this->connectionMock
			->expects($this->once())
			->method('keyLookupByEmail')
			->willReturn($lookupResult);

		$this->expectException(ThreemaChannelResultException::class);
		$this->expectExceptionCode($lookupResult->getErrorCode());

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2EUnsupportedLookup()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelUnsupportedNotifiableTest();

		$this->expectException(ThreemaChannelException::class);

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2ECustom()
	{
		$customConnectionMock = $this->createMock(Connection::class);
		$customConnectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$customConnectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));

		$customChannel = new ThreemaChannel($customConnectionMock, self::PRIVATE_KEY);

		$notification = new ThreemaChannelTextMessageCustomNotificationTest($customChannel);
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->never())
			->method('sendE2E');

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2EResult()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(400, null));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);

		try {
			$channel->send($notifiable, $notification);
			$this->fail();
		} catch(ThreemaChannelResultException $exception) {
			$this->assertEquals(400, $exception->getCode());
			$this->assertEquals(400, $exception->getResult()->getErrorCode());
			$this->assertFalse($exception->getResult()->isSuccess());
		}
	}

	/**
	 * @throws ThreemaChannelResultException
	 */
	public function testTextMessageE2EMsgApiException()
	{
		$notification = new ThreemaChannelTextMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willThrowException(new Exception());
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));

		$this->expectException(ThreemaChannelException::class);

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification)->isSuccess();
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testImageMessageE2E()
	{
		$notification = new ThreemaChannelImageMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));
		$this->connectionMock
			->expects($this->once())
			->method('keyCapability')
			->willReturn(new CapabilityResult(200, 'image'));
		$this->connectionMock
			->expects($this->once())
			->method('uploadFile')
			->willReturn(new UploadFileResult(200, null));

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testInvalidImageMessageE2E()
	{
		$notification = new ThreemaChannelInvalidImageMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->expectException(ThreemaChannelException::class);

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testLocationMessageE2E()
	{
		$notification = new ThreemaChannelLocationMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testAudioMessageE2E()
	{
		$notification = new ThreemaChannelAudioMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));
		$this->connectionMock
			->expects($this->once())
			->method('keyCapability')
			->willReturn(new CapabilityResult(200, 'audio'));
		$this->connectionMock
			->expects($this->once())
			->method('uploadFile')
			->willReturn(new UploadFileResult(200, null));

		$audioMessage = $notification->toThreema($notifiable);
		$this->assertEquals(1, $audioMessage->getDuration());

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testInvalidAudioMessageE2E()
	{
		$notification = new ThreemaChannelInvalidAudioMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->expectException(ThreemaChannelException::class);

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testVideoMessageE2E()
	{
		$notification = new ThreemaChannelVideoMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));
		$this->connectionMock
			->expects($this->once())
			->method('keyCapability')
			->willReturn(new CapabilityResult(200, 'video'));
		$this->connectionMock
			->expects($this->exactly(2))
			->method('uploadFile')
			->willReturn(new UploadFileResult(200, null));

		$videoMessage = $notification->toThreema($notifiable);
		$this->assertEquals(10, $videoMessage->getDuration());

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testInvalidVideoMessageE2E()
	{
		$notification = new ThreemaChannelInvalidVideoMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->expectException(ThreemaChannelException::class);

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelException
	 * @throws ThreemaChannelResultException
	 */
	public function testFileMessageE2E()
	{
		$notification = new ThreemaChannelFileMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->connectionMock
			->expects($this->once())
			->method('sendE2E')
			->willReturn(new SendE2EResult(200, null));
		$this->connectionMock
			->expects($this->once())
			->method('fetchPublicKey')
			->willReturn(new FetchPublicKeyResult(200, self::PUBLIC_KEY));
		$this->connectionMock
			->expects($this->once())
			->method('keyCapability')
			->willReturn(new CapabilityResult(200, 'file'));
		$this->connectionMock
			->expects($this->exactly(2))
			->method('uploadFile')
			->willReturn(new UploadFileResult(200, null));

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$this->assertTrue($channel->send($notifiable, $notification)->isSuccess());
	}

	/**
	 * @throws ThreemaChannelResultException
	 */
	public function testUnsupportedMessageSimple()
	{
		$notification = new ThreemaChannelUnsupportedMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->expectException(ThreemaChannelException::class);

		$channel = new ThreemaChannel($this->connectionMock);
		$channel->send($notifiable, $notification);
	}

	/**
	 * @throws ThreemaChannelResultException
	 */
	public function testUnsupportedMessageE2E()
	{
		$notification = new ThreemaChannelUnsupportedMessageNotificationTest();
		$notifiable = new ThreemaChannelIdNotifiableTest();

		$this->expectException(ThreemaChannelException::class);

		$channel = new ThreemaChannel($this->connectionMock, self::PRIVATE_KEY);
		$channel->send($notifiable, $notification);
	}

	protected function setUp(): void
	{
		$this->connectionMock = $this->createMock(Connection::class);
	}
}

class ThreemaChannelIdNotifiableTest
{
	use Notifiable;

	public string $id = 'ECHOECHO';

	public function routeNotificationForThreema(mixed $notification): Receiver
	{
		return new Receiver($this->id, Receiver::TYPE_ID);
	}
}

class ThreemaChannelEmailNotifiableTest
{
	use Notifiable;

	public string $email = 'echo@echo.echo';

	public function routeNotificationForThreema(mixed $notification): Receiver
	{
		return new Receiver($this->email, Receiver::TYPE_EMAIL);
	}
}

class ThreemaChannelPhoneNotifiableTest
{
	use Notifiable;

	public string $phone = '12345678901';

	public function routeNotificationForThreema(mixed $notification): Receiver
	{
		return new Receiver($this->phone, Receiver::TYPE_PHONE);
	}
}

class ThreemaChannelInvalidNotifiableTest
{
	use Notifiable;
}

class ThreemaChannelUnsupportedNotifiableTest extends TestCase
{
	use Notifiable;

	public function routeNotificationForThreema(mixed $notification): Receiver
	{
		$customReceiverMock = $this->createMock(Receiver::class);
		$customReceiverMock
			->expects($this->once())
			->method('getParams')
			->willReturn([]);

		return $customReceiverMock;
	}
}

class ThreemaChannelTextMessageNotificationTest extends Notification
{
	public function toThreema(mixed $notifiable): ThreemaTextMessage
	{
		$message = new ThreemaTextMessage('Hello World');
		$message->setText('Hello World!');

		return $message;
	}
}

class ThreemaChannelImageMessageNotificationTest extends Notification
{
	public function toThreema(mixed $notifiable): ThreemaImageMessage
	{
		return new ThreemaImageMessage(__DIR__ . '/../../assets/image.jpg');
	}
}

class ThreemaChannelInvalidImageMessageNotificationTest extends Notification
{
	public function toThreema(mixed $notifiable): ThreemaImageMessage
	{
		$message = new ThreemaImageMessage(__DIR__ . '/../../assets/image.jpg');
		$message->setPath(__DIR__ . '/../../assets/image.mp3');

		return $message;
	}
}

class ThreemaChannelLocationMessageNotificationTest extends Notification
{
	public function toThreema(mixed $notifiable): ThreemaLocationMessage
	{
		$message = new ThreemaLocationMessage(1.000000, 1.000000);
		$message->setLatitude(2.000000);
		$message->setLongitude(2.000000);
		$message->setAccuracy(1.0);
		$message->setPoiName('Hello World');
		$message->setPoiAddress('Hello 1, World');

		return $message;
	}
}

class ThreemaChannelAudioMessageNotificationTest extends Notification
{
	/**
	 * @throws ThreemaChannelException
	 */
	public function toThreema(mixed $notifiable): ThreemaAudioMessage
	{
		$message = new ThreemaAudioMessage(__DIR__ . '/../../assets/audio.mp3');
		$message->setPath(__DIR__ . '/../../assets/audio.mp3');

		return $message;
	}
}

class ThreemaChannelInvalidAudioMessageNotificationTest extends Notification
{
	/**
	 * @throws ThreemaChannelException
	 */
	public function toThreema(mixed $notifiable): ThreemaAudioMessage
	{
		$message = new ThreemaAudioMessage(__DIR__ . '/../../assets/audio.mp3');
		$message->setPath(__DIR__ . '/../../assets/audio.mp4');

		return $message;
	}
}

class ThreemaChannelVideoMessageNotificationTest extends Notification
{
	/**
	 * @throws ThreemaChannelException
	 */
	public function toThreema(mixed $notifiable): ThreemaVideoMessage
	{
		return new ThreemaVideoMessage(__DIR__ . '/../../assets/video.mp4');
	}
}

class ThreemaChannelInvalidVideoMessageNotificationTest extends Notification
{
	/**
	 * @throws ThreemaChannelException
	 */
	public function toThreema(mixed $notifiable): ThreemaVideoMessage
	{
		$message = new ThreemaVideoMessage(__DIR__ . '/../../assets/video.mp4');
		$message->setThumbnailPath(__DIR__ . '/../../assets/video.jpg');
		$message->setPath(__DIR__ . '/../../assets/video.mp3');

		return $message;
	}
}

class ThreemaChannelFileMessageNotificationTest extends Notification
{
	public function toThreema(mixed $notifiable): ThreemaFileMessage
	{
		$message = new ThreemaFileMessage(__DIR__ . '/../../assets/video.mp4');
		$message->setPath(__DIR__ . '/../../assets/audio.mp3');
		$message->setName('HelloWorld.mp3');
		$message->setCaption('Hello World!');
		$message->setType(1);
		$message->setThumbnailPath(__DIR__ . '/../../assets/image.jpg');

		return $message;
	}
}

class ThreemaChannelUnsupportedMessageNotificationTest extends Notification
{
	public function toThreema(mixed $notifiable): ThreemaMessage
	{
		return new ThreemaChannelUnsupportedMessageTest();
	}
}

class ThreemaChannelTextMessageCustomNotificationTest extends Notification
{
	private ThreemaChannel $channel;

	public function __construct(ThreemaChannel $channel)
	{
		$this->channel = $channel;
	}

	public function toThreema(mixed $notifiable): ThreemaTextMessage
	{
		$message = new ThreemaTextMessage('Hello World!', null);
		$message->setChannel($this->channel);

		return $message;
	}
}

class ThreemaChannelUnsupportedMessageTest extends ThreemaMessage
{
}
