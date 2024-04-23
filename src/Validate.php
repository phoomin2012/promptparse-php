<?php

namespace phumin\PromptParse;

use phumin\PromptParse\Exception\ParseErrorException;
use phumin\PromptParse\Library\TLV;

class Validate
{

  /**
   * Validate & extract data from Slip Verify QR (for use with Bank Open API)
   * 
   * @param string $payload QR Code Payload
   * @return null|array Array of Bank code and Transaction reference in order or null if payload invalid
   * @throws ParseErrorException 
   */
  public static function slipVerify(string $payload)
  {
    $ppqr = Parser::parse($payload, true);

    $apiType = $ppqr->getTagValue("00", "00");
    $sendingBank = $ppqr->getTagValue("00", "01");
    $transRef = $ppqr->getTagValue("00", "02");

    if ($apiType !== "000001" || !$sendingBank || !$transRef) return null;

    return [$sendingBank, $transRef];
  }

  /**
   * Verify QR Code Payload
   * 
   * @param string $payload QR Code Payload
   * @return bool Resolt of verify
   */
  public static function verify(string $payload)
  {
    if (preg_match("/^\d{4}.+/", $payload) === 0) return false;

    $excepted = substr($payload, -4);
    $checkPayload = substr($payload, 0, strlen($payload) - 4);
    $calculated = TLV::checksum($checkPayload);

    return $excepted === strtoupper($calculated);
  }
}
