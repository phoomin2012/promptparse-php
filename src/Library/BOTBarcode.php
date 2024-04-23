<?php

namespace phumin\PromptParse\Library;

use phumin\PromptParse\Generate\PromptPay;

class BOTBarcode
{
  public string $billerId;
  public string $ref1;
  public string $ref2;
  public float $amount;

  public function __construct(string $billerId, string $ref1, string $ref2 = null, float $amount = null)
  {
    $this->billerId = $billerId;
    $this->ref1 = $ref1;
    $this->ref2 = $ref2;
    $this->amount = $amount;
  }

  public static function fromString(string $payload)
  {
    if (strpos("|", $payload) !== 0) return null;

    $data = explode("\r", substr($payload, 1), 4);
    if (count($data) != 4) return null;

    list($billerId, $ref1, $ref2, $amount) = $data;
    $amount = (float) number_format((int) $amount / 100, 2, '.', '');

    return new BOTBarcode($billerId, $ref1, strlen($ref2) > 0 ? $ref2 : null, $amount);
  }

  public function toString()
  {
    $amountStr = $this->amount ? number_format($this->amount * 100, 0) : '0';
    return "|" . $this->billerId . "\r" . $this->ref1 . "\r" . ($this->ref2 ?? '') . "\r" . $amountStr;
  }

  public function toQrTag30()
  {
    return PromptPay::billPayment($this->billerId, $this->ref1, $this->ref2, $this->amount);
  }
}
