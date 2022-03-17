<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploaderAvatar extends AbstractController
{
    private $uploadDirectory;
    private $avatarDirectory;
    private $tricksDirectory;
    private $slugger;

    public function __construct($uploadDirectory, $avatarDirectory, $tricksDirectory, SluggerInterface $slugger)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->avatarDirectory = $avatarDirectory;
        $this->tricksDirectory = $tricksDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, Request $request)
    {
        $directory = $this->getTricksDirectory();
        if (in_array($request->getPathInfo(), ['/profile', '/profile/change_picture', '/register'])) {
            $directory = $this->getAvatarDirectory();
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        try {
            $file->move($directory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file 
            $this->addFlash('notice', $e->getMessage());
            return $this->redirectToRoute('app_user'); // for example
        }
        return $fileName;
    }

    public function getAvatarDirectory()
    {
        return $this->avatarDirectory;
    }

    public function getUploadDirectory()
    {
        return $this->uploadDirectory;
    }

    public function getTricksDirectory()
    {
        return $this->tricksDirectory;
    }
}
