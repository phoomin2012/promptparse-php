# PromptParse PHP ![Packagist Version](https://img.shields.io/packagist/v/phumin/promptparse)

"All-in-one PHP library for PromptPay & EMVCo QR Codes"

No dependency. Just install and enjoy!

This is a PHP port of [maythiwat/promptparse](https://github.com/maythiwat/promptparse)

# Features

- **Parse** — PromptPay & EMVCo QR Code data strings into object
- **Generate** — QR Code data from pre-made templates (for example: PromptPay AnyID, PromptPay Bill Payment, TrueMoney, etc.)
- **Manipulate** — any values from parsed QR Code data (for example: transfer amount, account number) and encodes back into QR Code data
- **Validate** — checksum and data structure for known QR Code formats (for example: Slip Verify API Mini QR)

# Usage

## Parsing data and get value from tag

```php
use phumin\PromptParse\Parser;

// Example data
$ppqr = Parser::parse("000201010211...");

// Get Value of Tag ID '00'
$ppqr->getTagValue("00") // Returns '01'
```

## Build QR data and append CRC tag
```php
use phumin\PromptParse\Library\TLV;

// Example data
$data = [
  TLV::tag("00", "01"),
  TLV::tag("01", "11"),
  ...
];

// Set CRC Tag ID '63'
TLV::withCrcTag(TLV::encode($data), '63'); // Returns '000201010211...'
```

## Generate PromptPay Bill Payment QR
```php
use phumin\PromptParse\Generate;

$payload = Generate::billPayment("1xxxxxxxxxxxx", "300.0", "INV12345");

// TODO: Create QR Code from payload
```

## Validate & extract data from Slip Verify QR
```php
use phumin\PromptParse\Validate;

$data = Validate::slipVerify("00550006000001...");

list($sendingBank, $transRef) = $data;
// or
$sendingBank = $data[0];
$transRef = $data[1];

// TODO: Inquiry transaction from Bank Open API
```

## Convert BOT Barcode to PromptPay QR Tag 30 (Bill Payment)
```php
use phumin\PromptParse\Parser;

$botBarcode = Validate::parseBarcode("|310109999999901\r...");

$payload = $botBarcode->toQrTag30();

// TODO: Create QR Code from payload
```

# References
- [EMV QR Code](https://www.emvco.com/emv-technologies/qrcodes/)
- [Thai QR Payment Standard](https://www.bot.or.th/content/dam/bot/fipcs/documents/FPG/2562/ThaiPDF/25620084.pdf)
- [Slip Verify API Mini QR Data](https://developer.scb/assets/documents/documentation/qr-payment/extracting-data-from-mini-qr.pdf)
- [BOT Barcode Standard](https://www.bot.or.th/content/dam/bot/documents/th/our-roles/payment-systems/about-payment-systems/Std_Barcode.pdf)

# License
This project is MIT licensed (see [LICENSE.md](LICENSE.md))
