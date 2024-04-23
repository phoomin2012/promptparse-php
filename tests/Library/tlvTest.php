<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use phumin\PromptParse\Library\TLV;

final class tlvTest extends TestCase
{
  public function testEncode()
  {
    $encoded = TLV::encode([TLV::tag('00', '00')]);
    $this->assertSame($encoded, "000200");
  }

  public function testDecode()
  {
    $payload = "000200";

    $tlvs = TLV::decode($payload);

    $this->assertCount(1, $tlvs);
    $this->assertEquals(TLV::tag('00', '00'), $tlvs[0]);
  }
}
