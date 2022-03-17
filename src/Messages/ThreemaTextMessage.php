<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Notifications\Channels\ThreemaChannel;

class ThreemaTextMessage extends ThreemaMessage
{
	protected string $text;

	public function __construct(string $text, ?ThreemaChannel $channel = null)
	{
		parent::__construct($channel);

		$this->text = $text;
	}

	public function getText(): string
	{
		return $this->text;
	}

	public function setText(string $text): self
	{
		$this->text = $text;

		return $this;
	}
}
