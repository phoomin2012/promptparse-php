<?php

declare(strict_types=1);

namespace phumin\PromptParse\Tests;

use PHPUnit\Framework\TestCase;
use phumin\PromptParse\Exception\ChecksumInvalidException;
use phumin\PromptParse\Exception\PayloadInvalidException;
use phumin\PromptParse\Generate;
use phumin\PromptParse\Parser;

final class parserTest extends TestCase
{
  public function testParsePass(): void
  {
    $payload = Generate::slipVerify('004', '0000000000');
    $parsed = Parser::parse($payload, true);

    $this->assertSame("004", $parsed->getTagValue("00", "02"));
  }

  public function testInvalidPayload(): void
  {
    $this->expectException(PayloadInvalidException::class);

    Parser::parse("0AA00000000000000000", true);
  }

  public function testInvalidChecksum(): void
  {
    $this->expectException(ChecksumInvalidException::class);

    $payload = Generate::slipVerify('004', '0000000000');
    $payload = substr_replace($payload, "AAAA", -4);

    Parser::parse($payload, true);
  }
}
