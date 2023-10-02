<?php

namespace Plugse\Ctrl\helpers;

use Plugse\Fp\File;

class FileUpload
{
    private $fileMaxSize;
    private $fileTypes;
    private $file;
    private $filePath;
    private $fileDeletedPath;

    private $errorMessage;
    private $uploadFileErrors;

    public function __construct(
        string $filePath = './storage/',
        int $fileMaxSize = 25 * 1024 * 1024,
        array $fileTypes = []
    ) {
        $this->setFileErros();
        $this->filePath = $filePath;
        $this->fileDeletedPath = './storage/deleteds/';
        $this->fileMaxSize = $fileMaxSize;
        $this->fileTypes = (empty($fileTypes) ? ['application/pdf', 'image/png', 'image/jpeg'] : $fileTypes);
    }

    public function validate(array $file)
    {
        $this->file = $file;

        return $this->validateError() and $this->validateSize() and $this->validateType();
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function uploadFile()
    {
        File::createPathIfNotExists($this->filePath);
        $newName = $this->getFileName();
        $filename = $this->filePath . $newName;

        if (file_exists($filename)) {
            unset($filename);
        }

        move_uploaded_file($this->file['tmp_name'], $filename);

        return $newName;
    }

    public function getFileName(): string
    {
        $name = date('YmdHis');

        $pathInfo = pathinfo($this->file['name']);
        $extension = $pathInfo['extension'];

        return "{$name}.{$extension}";
    }

    public function getFileExtension(): string
    {
        $pathInfo = pathinfo($this->file['name']);

        return $pathInfo['extension'];
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getStorageUrl()
    {
        $urlProtocol = "{$_SERVER['REQUEST_SCHEME']}://";
        $urlServer = "{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}";
        $urlScript = dirname($urlServer) . '/src';
        $urlScriptPieces = explode('/', $urlScript);
        $storageDirPieces = explode('/', $this->getFilePath());

        foreach ($storageDirPieces as $storagePiece) {
            if ($storagePiece === '..') {
                array_pop($urlScriptPieces);
                array_shift($storageDirPieces);
            }
        }
        if (!empty($urlScriptPieces)) {
            return $urlProtocol . implode('/', $urlScriptPieces) . '/' . implode('/', $storageDirPieces);
        } else {
            return "{$urlProtocol}{$urlScript}/storage/";
        }
    }

    public function setTrashFolder(string $folder)
    {
        $this->fileDeletedPath = $folder;
    }

    public function deleteFile(string $fileBaseName)
    {
        $filename = $this->filePath . $fileBaseName;

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function trashFile(string $fileBaseName)
    {
        $filename = $this->filePath . $fileBaseName;
        File::createPathIfNotExists($this->fileDeletedPath);

        if (file_exists($filename)) {
            rename($filename, $this->fileDeletedPath . $fileBaseName);

            return true;
        } else {
            $this->errorMessage = 'Aqruivo não encontrado';

            return false;
        }
    }

    private function validateSize()
    {
        if ($this->file['size'] > $this->fileMaxSize) {
            $this->errorMessage = 'O arquivo ultrapassou o tamanho máximo permitido';

            return false;
        }

        return true;
    }

    private function validateType()
    {
        if (!in_array($this->file['type'], $this->fileTypes)) {
            $this->errorMessage = 'Tipo de arquivo não esperado';

            return false;
        }

        return true;
    }

    private function validateError()
    {
        if ($this->file['error'] != 0) {
            $this->errorMessage = $this->uploadFileErrors[$this->file['error']];

            return false;
        }

        return true;
    }

    private function setFileErros()
    {
        $this->uploadFileErrors = [
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk.',
            8 => 'A PHP extension stopped the file upload.',
        ];
    }
}
