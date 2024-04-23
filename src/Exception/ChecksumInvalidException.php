<?php

namespace phumin\PromptParse\Exception;

use Exception;
use Throwable;

class ChecksumInvalidException extends Exception
{
  public function __construct($code = 0, Throwable $previous = null)
  {
    parent::__construct("Checksum invalid", $code, $previous);
  }
}
