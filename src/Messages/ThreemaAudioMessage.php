<?php

namespace Illuminate\Notifications\Messages;

use Exception;
use FFMpeg\FFProbe;
use Illuminate\Notifications\Channels\ThreemaChannel;
use Illuminate\Notifications\Exceptions\ThreemaChannelException;

class ThreemaAudioMessage extends ThreemaMessage
{
	protected string $path;
	private int $duration;

	/**
	 * @throws ThreemaChannelException
	 */
	public function __construct(string $path, ?ThreemaChannel $channel = null)
	{
		parent::__construct($channel);

		$this->path = $path;
		$this->duration = $this->calculateDuration();
	}

	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @throws ThreemaChannelException
	 */
	public function setPath(string $path): self
	{
		$this->path = $path;
		$this->duration = $this->calculateDuration();

		return $this;
	}

	public function getDuration(): int
	{
		return $this->duration;
	}

	/**
	 * @throws ThreemaChannelException
	 */
	private function calculateDuration(): int
	{
		try {
			$ffmpeg = FFProbe::create();
			$audio = $ffmpeg->format($this->path);

			return (int)$audio->get('duration');
		} catch(Exception $exception) {
			throw new ThreemaChannelException('The underlying FFMpeg has thrown an exception.', 0, $exception);
		}
	}
}
