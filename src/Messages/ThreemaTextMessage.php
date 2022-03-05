<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Notifications\Channels\ThreemaChannel;

class ThreemaTextMessage extends ThreemaMessage
{
	public string $text;

	public function __construct(string $text, ?ThreemaChannel $channel = null)
	{
		parent::__construct($channel);

		$this->text = $text;
	}
}
