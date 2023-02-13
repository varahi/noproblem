<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class FileService
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
     * @param string $fileName
     * @return void
     */
    public function downloadFile($fileName)
    {
        $file = $this->targetCsvDirectory . DIRECTORY_SEPARATOR . $fileName;
        $fileLen = filesize($file);
        $ext = strtolower(substr(strrchr($fileName, '.'), 1));
        switch ($ext) {
            case 'txt':
                $cType = 'text/plain';
                break;
            case 'pdf':
                $cType = 'application/pdf';
                break;
            case 'zip':
                $cType = 'application/zip';
                break;
            case 'doc':
                $cType = 'application/msword';
                break;
            case 'xls':
                $cType = 'application/vnd.ms-excel';
                break;
            case 'csv':
                $cType = 'application/vnd.ms-excel';
                break;
            case 'ppt':
                $cType = 'application/vnd.ms-powerpoint';
                break;
            case 'gif':
                $cType = 'image/gif';
                break;
            case 'png':
                $cType = 'image/png';
                break;
            case 'jpeg':
            case 'jpg':
                $cType = 'image/jpg';
                break;
            case 'mp3':
                $cType = 'audio/mpeg';
                break;
            case 'wav':
                $cType = 'audio/x-wav';
                break;
            case 'mpeg':
            case 'mpg':
            case 'mpe':
                $cType = 'video/mpeg';
                break;
            case 'mov':
                $cType = 'video/quicktime';
                break;
            case 'avi':
                $cType = 'video/x-msvideo';
                break;

            //forbidden filetypes
            case 'inc':
            case 'conf':
            case 'sql':
            case 'cgi':
            case 'htaccess':
            case 'php':
            case 'php3':
            case 'php4':
            case 'php5':
                exit;

            case 'exe':
            default:
                $cType = 'application/octet-stream';
                break;
        }

        $headers = array(
            'Pragma'                    => 'public',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => $cType,
            'Content-Disposition'       => 'attachment; filename="'. $fileName .'"',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Length'            => $fileLen
        );

        $response = new Response();
        foreach ($headers as $header => $data) {
            $response->headers->set($header, $data);
        }
        $response->sendHeaders();
        @readfile($file);
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
