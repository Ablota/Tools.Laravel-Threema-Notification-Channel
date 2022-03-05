<?php

namespace Illuminate\Notifications\Channels;

use Illuminate\Notifications\Exceptions\ThreemaChannelException;
use Illuminate\Notifications\Messages\ThreemaTextMessage;
use Illuminate\Notifications\Notification;
use Threema\MsgApi\Commands\Results\Result;
use Threema\MsgApi\Connection;
use Threema\MsgApi\Core\Exception;
use Threema\MsgApi\Helpers\E2EHelper;
use Threema\MsgApi\Receiver;

class ThreemaChannel
{
	protected Connection $connection;
	protected ?string $privateKey;

	/**
	 * @throws ThreemaChannelException
	 */
	public function __construct(Connection $connection, ?string $privateKey = null)
	{
		if ($privateKey !== null) {
			if (@hex2bin($privateKey) === false) {
				throw new ThreemaChannelException(
					'The provided private key for sending E2E messages is invalid.'
				);
			}

			$privateKey = hex2bin($privateKey);
		}

		$this->connection = $connection;
		$this->privateKey = $privateKey;
	}

	/**
	 * @throws ThreemaChannelException
	 */
	public function send(mixed $notifiable, Notification $notification): ?Result
	{
		$message = $notification->toThreema($notifiable);
		$connection = $message->channel ? $message->channel->connection : $this->connection;
		$privateKey = $message->channel ? $message->channel->privateKey : $this->privateKey;

		if (!$receiver = $notifiable->routeNotificationFor('threema', $notification)) {
			throw new ThreemaChannelException('Notifiable is missing "routeNotificationForThreema" function.');
		}

		if ($privateKey === null && $message instanceof ThreemaTextMessage) {
			$result = $connection->sendSimple($receiver, $message->text);
		} else {
			$e2eHelper = new E2EHelper($privateKey, $connection);

			$receiver = $receiver->getParams();
			if (array_key_exists(Receiver::TYPE_ID, $receiver)) {
				$threemaId = $receiver[Receiver::TYPE_ID];
			} else {
				if (array_key_exists(Receiver::TYPE_EMAIL, $receiver)) {
					$lookup = $connection->keyLookupByEmail($receiver[Receiver::TYPE_EMAIL]);
				} else if (array_key_exists(Receiver::TYPE_PHONE, $receiver)) {
					$lookup = $connection->keyLookupByPhoneNumber($receiver[Receiver::TYPE_PHONE]);
				}

				if (isset($lookup)) {
					if ($lookup->isSuccess()) {
						$threemaId = $lookup->getId();
					} else {
						return $lookup;
					}
				} else {
					throw new ThreemaChannelException('This lookup type is not supported by Laravel Threema.');
				}
			}

			try {
				if ($message instanceof ThreemaTextMessage) {
					$result = $e2eHelper->sendTextMessage($threemaId, $message->text);
				} else {
					throw new ThreemaChannelException('This message type is not supported by Laravel Threema.');
				}
			} catch (Exception $exception) {
				throw new ThreemaChannelException('The underlying Threema MsgApi has thrown an exception.', 0, $exception);
			}
		}

		return $result;
	}
}
