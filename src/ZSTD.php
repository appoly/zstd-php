<?php

namespace Appoly\ZstdPhp;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ZSTD
{
    /**
     * Compress a file using zstd.
     *
     * @param string      $inputPath  Path to the input file.
     * @param string|null $outputPath Optional path for the output file.
     *
     * @return string The command output.
     *
     * @throws \RuntimeException If the compression process fails.
     */
    public static function compress(string $inputPath, ?string $outputPath = null): string
    {
        $zstd = self::getZstdPath();

        if (!empty($outputPath)) {
            $command = [$zstd, '--force', '-o', $outputPath, $inputPath];
        } else {
            $command = [$zstd, '--force', '-o', $inputPath . '.zst', $inputPath];
        }

        $process = new Process($command);
        $process->mustRun(); // Automatically throws an exception if the process fails

        return $process->getOutput();
    }

    /**
     * Decompress a file using zstd.
     *
     * @param string      $inputPath  Path to the compressed file.
     * @param string|null $outputPath Optional path for the decompressed file.
     *
     * @return string The command output.
     *
     * @throws \RuntimeException If the decompression process fails.
     */
    public static function decompress(string $inputPath, ?string $outputPath = null): string
    {
        $zstd = self::getZstdPath();

        if (!empty($outputPath)) {
            $command = [$zstd, '--force', '-d', '-o', $outputPath, $inputPath];
        } else {
            $command = [$zstd, '--force', '-d', $inputPath];
        }

        $process = new Process($command);
        $process->mustRun();

        return $process->getOutput();
    }

    /**
     * Compress data from a stream, providing output in chunks.
     *
     * @param mixed      $inputStream    The input stream or data to compress.
     * @param callable   $outputCallback Callback function to handle each output chunk.
     * @param float|null $timeout        Optional timeout in seconds (null for default).
     *
     * @return void
     *
     * @throws \RuntimeException If the stream compression fails.
     */
    public static function compressDataFromStream($inputStream, callable $outputCallback, ?float $timeout = null): void
    {
        $zstd = self::getZstdPath();
        self::runStreamProcess([$zstd, '--force'], $inputStream, $outputCallback, 'Stream compression failed', $timeout);
    }

    /**
     * Decompress data from a stream, providing output in chunks.
     *
     * @param mixed      $inputStream    The input stream or data to decompress.
     * @param callable   $outputCallback Callback function to handle each output chunk.
     * @param float|null $timeout        Optional timeout in seconds (null for default).
     *
     * @return void
     *
     * @throws \RuntimeException If the stream decompression fails.
     */
    public static function decompressDataFromStream($inputStream, callable $outputCallback, ?float $timeout = null): void
    {
        $zstd = self::getZstdPath();
        self::runStreamProcess([$zstd, '--force', '-d'], $inputStream, $outputCallback, 'Stream decompression failed', $timeout);
    }

    /**
     * Run a stream process and handle output.
     *
     * @param array      $command        Command to execute.
     * @param mixed      $inputStream    The input stream or data.
     * @param callable   $outputCallback Callback function to handle each output chunk.
     * @param string     $errorMessage   Error message prefix if the process fails.
     * @param float|null $timeout        Optional timeout in seconds.
     *
     * @return void
     *
     * @throws \RuntimeException If the process fails.
     */
    private static function runStreamProcess(array $command, $inputStream, callable $outputCallback, string $errorMessage, ?float $timeout = null): void
    {
        $process = new Process($command);
        if ($timeout !== null) {
            $process->setTimeout($timeout);
        }
        $process->setInput($inputStream);
        $process->start();

        foreach ($process as $type => $data) {
            if ($type === Process::OUT) {
                $outputCallback($data);
            }
        }

        $process->wait();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($errorMessage . ': ' . $process->getErrorOutput());
        }
    }

    /**
     * Retrieve the path to the zstd executable.
     *
     * @return string The path to the zstd executable.
     *
     * @throws \Exception If zstd is not installed.
     */
    private static function getZstdPath(): string
    {
        $finder = new ExecutableFinder();
        $zstd = $finder->find('zstd');

        if (!$zstd) {
            throw new \Exception('zstd is not installed');
        }

        return $zstd;
    }
}