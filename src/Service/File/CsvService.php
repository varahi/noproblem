<?php

namespace App\Service\File;

use Symfony\Component\HttpFoundation\Response;

class CsvService
{
    private $targetCsvDirectory;

    /**
     * @param string $publicDir
     */
    public function __construct(
        string $targetCsvDirectory
    ) {
        $this->targetCsvDirectory = $targetCsvDirectory;
    }

    /**
     * Writes information into a csv-file
     *
     * @param $fileName
     * @param $mode
     * @param $record
     * @return void
     */
    public function writeCSVFileEntry($fileName, $record, $mode)
    {
        $fullFileName = $this->targetCsvDirectory . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($this->targetCsvDirectory)) {
            mkdir($this->targetCsvDirectory);
        }

        if ($record) {
            $handle = fopen($fullFileName, $mode);
            fwrite($handle, utf8_decode($record));
            fclose($handle);
        }
    }
}
