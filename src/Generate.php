<?php

namespace phumin\PromptParse;

use phumin\PromptParse\Library\BOTBarcode;
use phumin\PromptParse\Library\TLV;

class Generate
{
  /**
   * Generate BOT Barcode
   * 
   * @param string $billerId - Biller ID (Tax ID + Suffix)
   * @param string $ref1 - Reference No. 1 / Customer No.
   * @param null|string $ref2 - Reference No. 2
   * @param null|float $amount - Transaction amount
   * @return BOTBarcode Barcode Payload
   */
  public static function botBarcode(string $billerId, string $ref1, ?string $ref2 = null, ?float $amount = null)
  {
    return new BOTBarcode($billerId, $ref1, $ref2, $amount);
  }

  /**
   * Generate Slip Verify QR Code
   * 
   * This also called "Mini-QR" that embedded in slip used for verify transactions
   * 
   * @param string $sendingBank - Bank code
   * @param string $transRef - Transaction reference
   * @return string QR Code Payload
   */
  public static function slipVerify(string $sendingBank, string $transRef)
  {
    $payload = [
      TLV::tag("00", TLV::encode([
        TLV::tag("01", "000001"),
        TLV::tag("02", $sendingBank),
        TLV::tag("03", $transRef),
      ])),
      TLV::tag("51", "TH"),
    ];

    return TLV::withCrcTag(TLV::encode($payload), "91");
  }

  /**
   * Generate QR Code for TrueMoney Wallet
   * 
   * This QR Code can also be scanned with other apps,
   * just like a regular e-Wallet PromptPay QR
   * but `Personal Message (Tag 81)` will be ignored.
   * 
   * @param string $mobileNo - Mobile number
   * @param null|float $amount - Transaction amount
   * @param null|string $message - Personal message (Tag 81)
   * @return string QR Code Payload
   */
  public static function truemoney(string $mobileNo, ?float $amount, ?string $message)
  {
    $tag29 = [
      TLV::tag("00", "A000000677010111"),
      TLV::tag("03", "14000" . $mobileNo),
    ];

    $payload = [
      TLV::tag("00", "01"),
      TLV::tag("01", $amount ? "11" : "12"),
      TLV::tag("29", TLV::encode($tag29)),
      TLV::tag("53", "764"),
      TLV::tag("58", "TH"),
    ];

    if ($amount) array_push($payload, TLV::tag("54", number_format($amount, 2, '.', '')));

    if ($message) array_push($payload, TLV::tag("81", self::encodeTag81($message)));

    return TLV::withCrcTag(TLV::encode($payload), "91");
  }

  /**
   * Generate an `UCS-2`-like? Hex string for Tag 81
   * 
   * This method is equivalent to:
   * 
   * `Buffer.from(message, 'utf16le').swap16().toString('hex').toUpperCase()`
   * 
   * @param string $message - Message
   * @return string Hex string of provided message
   */
  private static function encodeTag81(string $message)
  {
    $chars = array_map(function ($char) {
      return str_pad(dechex(ord($char)), 4, STR_PAD_LEFT);
    }, str_split($message));

    return strtoupper(implode($chars));
  }
}
