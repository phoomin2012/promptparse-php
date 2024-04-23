<?php

declare(strict_types=1);

namespace phumin\PromptParse\Tests;

use PHPUnit\Framework\TestCase;
use phumin\PromptParse\Utils\Checksum;

final class checksumTest extends TestCase
{
  public function testChecksum(): void
  {
    $message = "00000";
    $ccr = new checksum(0xffff);
    $this->assertSame('c768', bin2hex($ccr->update($message)->finish()));
  }
}
