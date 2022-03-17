<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Notifications\Channels\ThreemaChannel;

class ThreemaFileMessage extends ThreemaMessage
{
	protected string $path;
	protected ?string $name;
	protected ?string $caption;
	protected ?int $type;
	protected ?string $thumbnailPath;

	public function __construct(
		string $path,
		?string $name = null,
		?string $caption = null,
		?int $type = null,
		?string $thumbnailPath = null,
		?ThreemaChannel $channel = null
	) {
		parent::__construct($channel);

		$this->path = $path;
		$this->name = $name;
		$this->caption = $caption;
		$this->type = $type;
		$this->thumbnailPath = $thumbnailPath;
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

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getCaption(): ?string
	{
		return $this->caption;
	}

	public function setCaption(?string $caption): self
	{
		$this->caption = $caption;

		return $this;
	}

	public function getType(): ?int
	{
		return $this->type;
	}

	public function setType(?int $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getThumbnailPath(): ?string
	{
		return $this->thumbnailPath;
	}

	public function setThumbnailPath(?string $thumbnailPath): self
	{
		$this->thumbnailPath = $thumbnailPath;

		return $this;
	}
}
