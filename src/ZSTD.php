<?php

namespace Appoly\ZstdPhp;

use Symfony\Component\Process\Process;

class ZSTD
{
    public static function compress(string $inputPath, ?string $outputPath = null): false|string
    {
        $zstd = self::getZstdPath();

        // Escape the input and output paths
        $inputPath = escapeshellarg($inputPath);
        $outputPath = escapeshellarg($outputPath);

        // Compress the data
        if (empty($outputPath)) {
            return exec("$zstd --force -o $inputPath.zst $inputPath");
        } else {
            return exec("$zstd --force -o $outputPath $inputPath");
        }
    }

    public static function decompress(string $inputPath, ?string $outputPath = null): false|string
    {
        $zstd = self::getZstdPath();

        // Escape the input and output paths
        $inputPath = escapeshellarg($inputPath);
        $outputPath = escapeshellarg($outputPath);

        // Decompress the data
        if (empty($outputPath)) {
            return exec("$zstd --force -d $inputPath");
        } else {
            return exec("$zstd --force -d -o $outputPath $inputPath");
        }
    }

    public static function compressDataToStream($inputStream, $outputStream): void
    {
        $zstd = self::getZstdPath();

        $process = new Process([
            $zstd,
            '--force',
        ]);

        $process->setInput($inputStream);
        $process->start();

        // Get output incrementally and write to the output stream
        foreach ($process as $type => $data) {
            if ($type === Process::OUT) {
                fwrite($outputStream, $data);
            }
        }

        $process->wait();
    }

    public static function decompressDataToStream(&$inputStream, &$outputStream): void
    {
        $zstd = self::getZstdPath();
        $process = new Process([
            $zstd,
            '--force',
            '-d',
        ]);

        $process->setInput($inputStream);
        $process->start();

        // Get output incrementally and write to the output stream
        foreach ($process as $type => $data) {
            if ($type === Process::OUT) {
                fwrite($outputStream, $data);
            }
        }

        $process->wait();
    }

    private static function getZstdPath(): string
    {
        // Find the local zstd library
        // Use either which zstd or where zstd depending on the OS
        $zstd = exec('which zstd');
        if (empty($zstd)) {
            $zstd = exec('where zstd');
        }

        // If not installed, throw an exception
        if (empty($zstd)) {
            throw new \Exception('zstd not installed');
        }

        return $zstd;
    }
}
