<?php

namespace Illuminate\Notifications\Messages;

use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Notifications\Channels\ThreemaChannel;
use Illuminate\Notifications\Exceptions\ThreemaChannelException;

class ThreemaVideoMessage extends ThreemaMessage
{
	protected string $path;
	protected string $thumbnailPath;
	private int $duration;

	/**
	 * @throws ThreemaChannelException
	 */
	public function __construct(string $path, ?string $thumbnailPath = null, ?ThreemaChannel $channel = null)
	{
		parent::__construct($channel);

		$this->path = $path;
		if($thumbnailPath === null) {
			$thumbnailPath = $this->generateThumbnailPath();
		}
		$this->thumbnailPath = $thumbnailPath;
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

	public function getThumbnailPath(): string
	{
		return $this->thumbnailPath;
	}

	public function setThumbnailPath(string $thumbnailPath): self
	{
		$this->thumbnailPath = $thumbnailPath;

		return $this;
	}

	public function getDuration(): int
	{
		return $this->duration;
	}

	/**
	 * @throws ThreemaChannelException
	 */
	private function generateThumbnailPath(): string
	{
		try {
			$file = tempnam(sys_get_temp_dir(), 'frame.jpg');

			$ffmpeg = FFMpeg::create();
			$video = $ffmpeg->open($this->path);
			$video
				->frame(TimeCode::fromSeconds(0))
				->save($file);

			return $file;
		} catch(Exception $exception) {
			throw new ThreemaChannelException('The underlying FFMpeg has thrown an exception.', 0, $exception);
		}
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
