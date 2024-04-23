<?php

namespace phumin\PromptParse\Exception;

use Exception;
use Throwable;

class ParseErrorException extends Exception
{
  public function __construct($code = 0, Throwable $previous = null)
  {
    parent::__construct("Cannot parse given payload", $code, $previous);
  }
}
