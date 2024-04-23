<?php

namespace phumin\PromptParse\Exception;

use Exception;
use Throwable;

class PayloadInvalidException extends Exception
{
  public function __construct($code = 0, Throwable $previous = null)
  {
    parent::__construct("Payload invalid", $code, $previous);
  }
}
