<?php

namespace Illuminate\Notifications\Exceptions;

use Exception;
use Threema\MsgApi\Commands\Results\Result;
use Throwable;

class ThreemaChannelResultException extends Exception
{
	protected Result $result;

	public function __construct(Result $result, ?Throwable $previous = null)
	{
		$this->result = $result;

		parent::__construct($result->getErrorMessage(), $result->getErrorCode(), $previous);
	}

	public function getResult(): Result
	{
		return $this->result;
	}
}
