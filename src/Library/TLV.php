<?php

namespace phumin\PromptParse\Library;

use phumin\PromptParse\Utils\Checksum;

class TLV
{
  /** Tag ID */
  public string $id;

  /** Tag Value */
  public int $length;

  /** Tag Length */
  public string $value;

  /**
   * Sub Tags
   * 
   * @var TLV[] | null
   */
  public $subTags = null;

  /**
   * Create new TLV Tag
   * 
   * @param string $id Tag ID
   * @param int $length Tag Value
   * @param string $value Tag Length
   * @param TLV[] | null $subTags Sub Tags
   */
  public function __construct(string $id, int $length, string $value, $subTags = null)
  {
    $this->id = $id;
    $this->length = $length;
    $this->value = $value;
    $this->subTags = $subTags;
  }

  /**
   * Decode TLV string into array of TLV Tags
   *
   * @param string $payload TLV string
   * @return TLV[] Array of TLV Tags
   */
  public static function decode(string $payload)
  {
    $tags = [];

    $idx = 0;
    while ($idx < strlen($payload)) {
      $data = substr($payload, $idx);

      $id = substr($data, 0, 2);
      $length = (int) substr($data, 2, 2);
      $value = substr($data, 4, $length);

      array_push($tags, new TLV($id, $length, $value));
      $idx += 4 + $length;
    }

    return $tags;
  }

  /**
   * Encode TLV Tags array into TLV string
   * 
   * @param TLV[] $tags Array of TLV Tags
   * @return string TLV string
   */
  public static function encode(array $tags)
  {
    $payload = "";

    foreach ($tags as $tag) {
      $payload .= $tag->id;
      $payload .= substr("00" . $tag->length, -2);

      if ($tag->subTags) {
        $payload .= self::encode($tag->subTags);
      }

      $payload .= $tag->value;
    }

    return $payload;
  }

  /**
   * Generate CRC Checksum for provided string
   * 
   * @param string $payload Any string
   * @return string CRC Checksum
   */
  public static function checksum(string $payload)
  {
    $crc = new checksum(0xffff);
    $crc->update($payload);

    $sum = $crc->finish();
    return bin2hex($sum);
  }

  /**
   * Get TLV string combined with CRC Tag
   * 
   * @param string $payload TLV string (without CRC Tag)
   * @param string $crcTagId CRC Tag ID
   * @return string TLV string + CRC Tag ID + CRC Length + CRC Checksum
   */
  public static function withCrcTag(string $payload, string $crcTagId)
  {
    $payload .= substr("00" . $crcTagId, -2);
    $payload .= "04";
    $payload .= strtoupper(self::checksum($payload));
    return $payload;
  }

  /**
   * Create new TLV Tag
   * 
   * @param string $tagId Tag ID
   * @param string $value Tag Value
   * @return TLV TLV Tag
   */
  public static function tag(string $tagId, string $value)
  {
    return new TLV($tagId, strlen($value), $value);
  }
}
