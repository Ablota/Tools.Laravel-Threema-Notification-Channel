<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Notifications\Channels\ThreemaChannel;

abstract class ThreemaMessage
{
	public ?ThreemaChannel $channel;

	public function __construct(?ThreemaChannel $channel = null)
	{
		$this->channel = $channel;
	}
}
