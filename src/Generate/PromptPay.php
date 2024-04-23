<?php

namespace phumin\PromptParse\Generate;

use phumin\PromptParse\Library\TLV;

class PromptPay
{
  const PROXY_TYPE_MSISDN = "01";
  const PROXY_TYPE_NATID = "02";
  const PROXY_TYPE_EWALLETID = "03";
  const PROXY_TYPE_BANKACC = "04";

  /**
   * Generate PromptPay AnyID (Tag 29) QR Code
   * 
   * @param mixed $type Proxy type
   * Proxy type can be
   * 
   * `PromptPay::PROXY_TYPE_MSISDN`
   * 
   * `PromptPay::PROXY_TYPE_NATID`
   * 
   * `PromptPay::PROXY_TYPE_EWALLETID`
   * 
   * `PromptPay::PROXY_TYPE_BANKACC`
   * 
   * @param string $target Recipient number
   * @param null|float $amount Transaction amount
   * @return string QR Code Payload 
   */
  public static function anyId(string $type, string $target, ?float $amount)
  {
    if ($type === self::PROXY_TYPE_MSISDN) $target = substr("0000000000000" . preg_replace("/^0/", "66", $target), "-13");

    $tag29 = [
      TLV::tag("00", "A000000677010111"),
      TLV::tag($type, $target),
    ];

    $payload = [
      TLV::tag("00", "01"),
      TLV::tag("01", $amount ? "11" : "12"),
      TLV::tag("29", TLV::encode($tag29)),
      TLV::tag("53", "764"),
      TLV::tag("58", "TH"),
    ];

    if ($amount) array_push($payload, TLV::tag("54", number_format($amount, 2, '.', '')));

    return TLV::withCrcTag(TLV::encode($payload), "63");
  }

  /**
   * Generate PromptPay Bill Payment (Tag 30) QR Code
   * 
   * @param string $billerId - Biller ID (National ID or Tax ID + Suffix)
   * @param string $ref1 - Reference 1
   * @param string|null $ref2 - Reference 2
   * @param string|null $ref3 - (Undocumented) Reference 3
   * @param float|null $amount - Transaction amount
   * @return string QR Code Payload 
   */
  public static function billPayment(string $billerId, string $ref1, ?string $ref2 = null, ?string $ref3 = null, ?float $amount = null)
  {
    $tag30 = [
      TLV::tag("00", "A000000677010112"),
      TLV::tag("01", $billerId),
      TLV::tag("02", $ref1),
    ];

    if ($ref2) array_push($tag30, TLV::tag("03", $ref2));

    $payload = [
      TLV::tag("00", "01"),
      TLV::tag("01", $amount ? "11" : "12"),
      TLV::tag("30", TLV::encode($tag30)),
      TLV::tag("53", "764"),
      TLV::tag("58", "TH"),
    ];

    if ($amount) array_push($payload, TLV::tag("54", number_format($amount, 2, '.', '')));

    if ($ref3) array_push($payload, TLV::tag("62", TLV::encode([TLV::tag("07", $ref3)])));

    return TLV::withCrcTag(TLV::encode($payload), "63");
  }
}
