<?php

namespace phumin\PromptParse;

use phumin\PromptParse\Exception\ChecksumInvalidException;
use phumin\PromptParse\Exception\ParseErrorException;
use phumin\PromptParse\Exception\PayloadInvalidException;
use phumin\PromptParse\Library\BOTBarcode;
use phumin\PromptParse\Library\EMVCoQR;
use phumin\PromptParse\Library\TLV;

class Parser
{
  /**
   * 
   * @param string $payload Payload to parse
   * @param bool $strict Throw exception if payload cannot parse
   * @param bool $subTags Parse sub tags
   * @return EMVCoQR Tag object
   * @throws ParseErrorException 
   */
  public static function parse(string $payload, $strict = false, $subTags = true)
  {
    if (preg_match("/^\d{4}.+/", $payload) === 0) throw new PayloadInvalidException();

    if ($strict) {
      $excepted = substr($payload, -4);
      $checkPayload = substr($payload, 0, strlen($payload) - 4);
      $calculated = TLV::checksum($checkPayload);

      if ($excepted !== strtoupper($calculated)) throw new ChecksumInvalidException();
    }

    $tags = TLV::decode($payload);
    if (count($tags) === 0) throw new ParseErrorException();

    if ($subTags) {
      foreach ($tags as $tag) {
        if (!is_string($tag->value) || preg_match("/^\d{4}.+/", $tag->value) === 0) continue;

        $sub = TLV::decode($tag->value);

        $skip = false;
        foreach ($sub as $value) {
          if ($value->length === 0 || $value->length !== strlen($value->value)) {
            $skip = true;
            break;
          }
        }

        if ($skip) continue;
        $tag->subTags = $sub;
      }
    }

    return new EMVCoQR($payload, $tags);
  }

  public static function parseBarcode(string $payload)
  {
    return BOTBarcode::fromString($payload);
  }
}
