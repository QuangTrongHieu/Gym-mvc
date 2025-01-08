<?php

namespace Core;

class ImageUploader
{
    private $uploadPath;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxSize = 5242880; // 5MB

    public function __construct($uploadPath = 'public/uploads/images')
    {
        $this->uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/gym/' . $uploadPath;
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    public function upload($file)
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new \RuntimeException('Invalid parameters.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('Exceeded filesize limit.');
            default:
                throw new \RuntimeException('Unknown errors.');
        }

        if ($file['size'] > $this->maxSize) {
            throw new \RuntimeException('Exceeded filesize limit.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new \RuntimeException('Invalid file format. Only JPG, PNG & GIF files are allowed.');
        }

        $extension = array_search($mimeType, [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ], true);

        $fileName = sprintf(
            '%s.%s',
            sha1_file($file['tmp_name']),
            $extension
        );

        if (!move_uploaded_file(
            $file['tmp_name'],
            $this->uploadPath . DIRECTORY_SEPARATOR . $fileName
        )) {
            throw new \RuntimeException('Failed to move uploaded file.');
        }

        return $fileName;
    }

    public function delete($fileName)
    {
        $filePath = $this->uploadPath . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
            return true;
        }
        return false;
    }
}
