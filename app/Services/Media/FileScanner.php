<?php

namespace App\Services\Media;

use Socket\Raw\Factory;
use Xenolope\Quahog\Client as ClamAV;
use Xenolope\Quahog\Result;
use Exception;

class FileScanner
{
    private string $socketAddress;

    public function __construct()
    {
        $this->socketAddress = env('CLAMAV_SOCKET', 'tcp://clam:3310');
    }

    /**
     * Create a new ClamAV client instance.
     * This ensures a fresh connection per scan.
     */
    private function client(): ClamAV
    {
        $socket = (new Factory())->createClient($this->socketAddress);
        return new ClamAV($socket);
    }

    /**
     * Scans a file and throws an exception if infected or unreadable.
     *
     * @param string $absolutePath
     * @return bool
     * @throws Exception
     */
    public function isClean(string $absolutePath): bool
    {
        if (!is_readable($absolutePath)) {
            throw new Exception("File is not readable: {$absolutePath}");
        }

        $data = file_get_contents($absolutePath);
        if ($data === false) {
            throw new Exception("Failed to read file: {$absolutePath}");
        }

        $result = $this->client()->scanStream($data);

        if (!$result instanceof Result) {
            throw new Exception("Invalid response from ClamAV.");
        }

        if ($result->isError()) {
            throw new Exception("ClamAV error: " . $result->getReason());
        }

        if ($result->isFound()) {
            throw new Exception("Virus detected: " . $result->getReason());
        }

        if (!$result->isOk()) {
            throw new Exception("Unexpected scan result: " . $result->getReason());
        }

        return true;
    }
}
