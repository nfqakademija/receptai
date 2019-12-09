<?php


namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const RECIPE_IMAGE = 'images';

    private $targetDirectory;

    private $requestStackContext;

    public function __construct($targetDirectory, RequestStackContext $requestStackContext)
    {
        $this->targetDirectory = $targetDirectory;
        $this->requestStackContext = $requestStackContext;
    }

    public function upload(UploadedFile $file, LoggerInterface $logger)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename
        );
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            $logger->critical('Image was not uploaded');
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
            ->getBasePath().'/uploads/'.$path;
    }
}
