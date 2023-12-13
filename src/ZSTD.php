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

    public static function compressDataFromStream(&$inputStream, $outputCallback): void
    {
        $zstd = self::getZstdPath();

        $process = new Process(
            [
                $zstd,
                '--force',
            ],
            null,
            null,
            null,
            0,
        );

        $process->setInput($inputStream);
        $process->start();

        // Get output incrementally
        foreach ($process as $type => $data) {
            if ($type === Process::OUT) {
                $outputCallback($data);
            }
        }

        $process->wait();
    }

    public static function decompressDataFromStream(&$inputStream, $outputCallback): void
    {
        $zstd = self::getZstdPath();
        $process = new Process(
            [
                $zstd,
                '--force',
                '-d',
            ],
            null,
            null,
            null,
            0,
        );

        $process->setInput($inputStream);
        $process->start();

        // Get output incrementally and execute the callback for each chunk
        foreach ($process as $type => $data) {
            if ($type === Process::OUT) {
                $outputCallback($data);
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
