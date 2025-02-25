# PHP ZSTD

PHP library to allow [Zstandard](https://facebook.github.io/zstd/) compression and decompression, without the need to install an additional PHP extension.

## Dependencies

ZSTD must be installed on your system. Example installation on Ubuntu:

```bash
sudo apt install zstd
```

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

If no output path is provided, the compressed file will be saved with the `.zst` extension added:

```php
ZSTD::compress('path/to/file'); // Creates path/to/file.zst
```

For decompression, if no output path is provided, the file will be decompressed in place:

```php
ZSTD::decompress('path/to/file.zst'); // Creates path/to/file
```

### Advanced

Decompress from a stream, and handle the output yourself in chunks. Can be used with custom filesystems etc to minimize memory usage.

```php
use Appoly\ZstdPhp\ZSTD;

$inputStream = fopen('path/to/file.zst', 'r');
$outputCallback = function ($outputChunk) {
    // Do something with the output chunk
    echo $outputChunk;
};

ZSTD::decompressDataFromStream(
    inputStream: $inputStream,
    outputCallback: $outputCallback
);

fclose($inputStream);
```

Compress from a stream, and handle the output yourself in chunks. Can be used with custom filesystems etc to minimize memory usage.

```php
use Appoly\ZstdPhp\ZSTD;

$inputStream = fopen('path/to/file', 'r');
$outputCallback = function ($outputChunk) {
    // Do something with the output chunk
    echo $outputChunk;
};

ZSTD::compressDataFromStream(
    inputStream: $inputStream,
    outputCallback: $outputCallback
);

fclose($inputStream);
```

You can also specify a timeout for stream operations:

```php
ZSTD::compressDataFromStream(
    inputStream: $inputStream,
    outputCallback: $outputCallback,
    timeout: 60.0 // Timeout in seconds
);
```

## Exception Handling

The library throws exceptions when operations fail:

- `\Exception` if ZSTD is not installed on the system
- `\RuntimeException` if compression or decompression processes fail

## License
MIT License

Copyright (c) 2025 Appoly Ltd

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.