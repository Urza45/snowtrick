<?php

namespace App\Services;

use App\Entity\Media;
use App\Entity\TypeMedia;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader extends AbstractController
{
    private $uploadDirectory;
    private $avatarDirectory;
    private $tricksDirectory;
    private $slugger;
    private $imageTypeArray = array(
        0 => 'UNKNOWN',
        1 => 'GIF',
        2 => 'JPEG',
        3 => 'PNG',
        4 => 'SWF',
        5 => 'PSD',
        6 => 'BMP',
        7 => 'TIFF_II',
        8 => 'TIFF_MM',
        9 => 'JPC',
        10 => 'JP2',
        11 => 'JPX',
        12 => 'JB2',
        13 => 'SWC',
        14 => 'IFF',
        15 => 'WBMP',
        16 => 'XBM',
        17 => 'ICO',
        18 => 'COUNT'
    );

    public function __construct($uploadDirectory, $avatarDirectory, $tricksDirectory, SluggerInterface $slugger)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->avatarDirectory = $avatarDirectory;
        $this->tricksDirectory = $tricksDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, Request $request, $type = 'tricks')
    {
        $directory = $this->getTricksDirectory();
        //if (in_array($request->getPathInfo(), ['/profile', '/profile/change_picture', '/register'])) {
        if ($type == 'avatar') {
            $directory = $this->getAvatarDirectory();
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $message = $this->imagesResizeCarre($file, $directory . '/' . $fileName, 1200, 'center', false);
            $message .= '<br/>'
                . $this->imagesResizeCarre($file, $directory . '/' . 'thumbs_' . $fileName, 200, 'center', true);
            //$file->move($directory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file
            //$this->addFlash('notice', $e->getMessage());
            return [
                'status' => 'fail',
                'message' => $e->getMessage()
            ];
        }

        return [
            'status' => 'success',
            'message' => $fileName
        ];
    }

    public function deleteFile(Media $media, TypeMedia $typeMedia, MediaRepository $repoMedia)
    {
        $message = '';
        if ($typeMedia->getGroupMedia() == 'Image') {
            // Suppression de la miniature
            if (!unlink($media->getThumbUrl())) {
                $message .= 'La miniature n\'a pas pas pu être supprimée';
            };
            // Suppression de l'image
            if (!unlink($media->getUrl())) {
                if ($message <> '') {
                    $message .= '<br/>';
                }
                $message .= 'La photographie n\'a pas pas pu être supprimée';
            };
        }
        if ($message <> '') {
            $message .= 'L\'entrée en base de données est conservée.';
            $message = '<p class="text-danger">' . $message . '</p>';
            return new Response($message);
        } else {
            // Suppression de l'entrée en base de données.
            $repoMedia->remove($media, true);
            return new Response('<p class="text-success">Le média a bien été supprimé.</p>');
        }
        return new Response('<p class="text-success">Pas de suppression</p>');
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

    /**
     * imagesResizeCarre
     *
     * @param  mixed $src
     * @param  mixed $dest
     * @param  mixed $largeur
     * @param  mixed $pos
     * @param  mixed $carre
     * @return void
     */
    private function imagesResizeCarre($src, $dest, $largeur, $pos, $carre = true)
    {
        list($srcX, $srcY, $type, $attr) = getimagesize($src);

        switch ($this->imageTypeArray[$type]) {
            case 'GIF':
                $imgSrc = imagecreatefromgif($src);
                break;
            case 'JPEG':
                $imgSrc = imagecreatefromjpeg($src);
                break;
            case 'PNG':
                $imgSrc = imagecreatefrompng($src);
                break;
            default:
                return 'Erreur de format';
        }

        if (empty($imgSrc)) {
            return false;
        }
        if ($srcX >= $srcY) {
            $dim = $srcX;
            $horizontale = true;
        } elseif ($srcX <= $srcY) {
            $dim = $srcY;
            $verticale = true;
        } else {
            $dim = $srcX;
        }
        //on determine le point de depart x,y
        if (isset($verticale)) {
            switch ($pos) {
                case "left":
                    $point_x_ref = "0";
                    $point_y_ref = "0";
                    break;
                case "right":
                    $point_x_ref = ($srcX) - ($dim);
                    $point_y_ref = "0";
                    break;
                default:
                    $point_x_ref = ($srcX / 2) - ($dim / 2);
                    $point_y_ref = "0";
                    break;
            }
        } elseif (isset($horizontale)) {
            switch ($pos) {
                case "top":
                    $point_x_ref = "0";
                    $point_y_ref = "0";
                    break;
                case "bottom":
                    $point_x_ref = "0";
                    $point_y_ref = ($srcY) - ($dim);
                    break;
                default:
                    $point_x_ref = "0";
                    $point_y_ref = ($srcY / 2) - ($dim / 2);
                    break;
            }
        }

        // Image carrée
        if ($carre === true) {
            $imDest = imagecreatetruecolor($largeur, $largeur);
        } else { // Image rectangulaire
            $ratio_orig = $srcX / $srcY;

            if (1 > $ratio_orig) {
                $width = round($largeur * $ratio_orig);
                $height = $largeur;
            } else {
                $width = $largeur;
                $height = round($largeur / $ratio_orig);
            }

            // Redimensionnement
            $imDest = imagecreatetruecolor($width, $height);
            imagecopyresampled(
                $imDest,
                $imgSrc,
                0,
                0,
                0,
                0,
                $width,
                $height,
                $srcX,
                $srcY
            );
            imagedestroy($imgSrc);
            imagejpeg($imDest, $dest, $largeur);
            imagedestroy($imDest);
            return true;
        }

        // Fond blanc
        $bgColor = imagecolorallocate($imDest, 255, 255, 255);
        imagefill($imDest, 0, 0, $bgColor);

        imagecopyresampled(
            $imDest,
            $imgSrc,
            0,
            0,
            $point_x_ref,
            $point_y_ref,
            $largeur,
            $largeur,
            $dim,
            $dim
        );
        imagedestroy($imgSrc);
        imagejpeg($imDest, $dest, $largeur);
        imagedestroy($imDest);
        return true;
    }
}
