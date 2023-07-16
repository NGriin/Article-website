<?php

namespace App\Service;

use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $slugger;
    private $filesystem;

    public function __construct(Filesystem $articleFileSystem, SluggerInterface $slugger)
    {

        $this->slugger = $slugger;
        $this->filesystem = $articleFileSystem;
    }

    public function uploadFile(File $file, ?string $oldFileName = null): string
    {
        $fileName = $this->slugger
            ->slug(pathinfo($file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename(), PATHINFO_FILENAME))
            ->append('-' . uniqid())
            ->append('.' . $file->guessExtension())
            ->toString();

        $stream = fopen($file->getPathname(), 'r');


        try {
            $this->filesystem->writeStream($fileName, $stream);
        } catch (\Exception $e) {
            echo 'Поймано исключение: ', $e->getMessage(), "\n";
        }

        if (is_resource($stream)) {
            fclose($stream);
        }


        if ($oldFileName && $this->filesystem->has($oldFileName)) {
            try {
                $this->filesystem->delete($oldFileName);
            } catch (\Exception $e) {
                echo 'Поймано исключение: ', $e->getMessage(), "\n";
            }
        }
//        $newFile = $file->move($this->uploadsPath, $fileName);

        return $fileName;
    }
}