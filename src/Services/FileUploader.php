<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;
    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }
    public function upload(UploadedFile $file)
    {
        $originalFileName= pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
        $safeFilename= $this->slugger->slug($originalFileName);
        $fileName = $safeFilename.'_'.uniqid().'.'.$file->guessExtension();
        try {
                $file->move($this->getTargetDirectory(),$fileName);
        }catch (FileException $exception){
            return null;
        }
        return $fileName;

    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

}