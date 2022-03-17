<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Notifications\Channels\ThreemaChannel;

class ThreemaImageMessage extends ThreemaMessage
{
	protected string $path;

	public function __construct(string $path, ?ThreemaChannel $channel = null)
	{
		parent::__construct($channel);

		$this->path = $path;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function setPath(string $path): self
	{
		$this->path = $path;

		return $this;
	}
}
