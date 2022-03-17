<?php

namespace Illuminate\Notifications\Messages;

use Illuminate\Notifications\Channels\ThreemaChannel;

class ThreemaLocationMessage extends ThreemaMessage
{
	protected float $latitude;
	protected float $longitude;
	protected ?float $accuracy;
	protected ?string $poiName;
	protected ?string $poiAddress;

	public function __construct(float $latitude, float $longitude, ?float $accuracy = null, ?string $poiName = null, ?string $poiAddress = null, ?ThreemaChannel $channel = null)
	{
		parent::__construct($channel);

		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->accuracy = $accuracy;
		$this->poiName = $poiName;
		$this->poiAddress = $poiAddress;
	}

	public function getLatitude(): float
	{
		return $this->latitude;
	}

	public function setLatitude(float $latitude): self
	{
		$this->latitude = $latitude;

		return $this;
	}

	public function getLongitude(): float
	{
		return $this->longitude;
	}

	public function setLongitude(float $longitude): self
	{
		$this->longitude = $longitude;

		return $this;
	}

	public function getAccuracy(): ?float
	{
		return $this->accuracy;
	}

	public function setAccuracy(?float $accuracy): self
	{
		$this->accuracy = $accuracy;

		return $this;
	}

	public function getPoiName(): ?string
	{
		return $this->poiName;
	}

	public function setPoiName(?string $poiName): self
	{
		$this->poiName = $poiName;

		return $this;
	}

	public function getPoiAddress(): ?string
	{
		return $this->poiAddress;
	}

	public function setPoiAddress(?string $poiAddress): self
	{
		$this->poiAddress = $poiAddress;

		return $this;
	}
}
