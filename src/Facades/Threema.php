<?php

namespace Illuminate\Notifications\Facades;

use Illuminate\Notifications\Channels\ThreemaChannel;
use Illuminate\Support\Facades\Facade;

class Threema extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return ThreemaChannel::class;
	}
}
