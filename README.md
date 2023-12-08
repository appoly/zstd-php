# PHP ZSTD

Can be used in PHP without the need to install an additional extension.

## Dependencies

ZSTD, example installation on Ubuntu:

```bash
sudo apt install zstd
````

## Installation

```bash
composer require appoly/zstd-php
```

## Usage

### Basic

Decompress a file on the local filesystem:

```php
use Appoly\ZstdPhp\ZSTD;

ZSTD::decompress('path/to/file.zst', 'path/to/output/file');
```

Compress a file on the local filesystem:

```php
use Appoly\ZstdPhp\ZSTD;

ZSTD::compress('path/to/file', 'path/to/output/file.zst');
```

### Advanced

Decompress from a stream (can be used with custom filesystems such as S3):

```php
use Appoly\ZstdPhp\ZSTD;

$inputStream = fopen('path/to/file.zst', 'r');
$outputStream = fopen('path/to/output/file', 'w');

ZSTD::decompressDataToStream($inputStream, $outputStream);

fclose($inputStream);
fclose($outputStream);
```

Compress to a stream (can be used with custom filesystems such as S3):

```php
use Appoly\ZstdPhp\ZSTD;

$inputStream = fopen('path/to/file', 'r');
$outputStream = fopen('path/to/output/file.zst', 'w');

ZSTD::compressDataToStream($inputStream, $outputStream);

fclose($inputStream);
fclose($outputStream);
```

## License
MIT License

Copyright (c) 2023 Appoly Ltd

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
