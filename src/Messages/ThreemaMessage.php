<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Notifications\Channels\ThreemaChannel;

abstract class ThreemaMessage
{
	protected ?ThreemaChannel $channel;

	public function __construct(?ThreemaChannel $channel = null)
	{
		$this->channel = $channel;
	}

	public function getChannel(): ?ThreemaChannel
	{
		return $this->channel;
	}

	public function setChannel(?ThreemaChannel $channel): self
	{
		$this->channel = $channel;

		return $this;
	}
}
