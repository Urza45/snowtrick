<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Captcha extends AbstractController
{

    private $policeCaptcha;

    public function __construct($police)
    {
        $this->policeCaptcha = $police;
    }

    /**
     * captcha
     * Display and store in session the value of a captcha
     *
     * @param  mixed $session
     * @return void
     */
    public function captcha(Session $session)
    {
        $captcha = mt_rand(1000, 9999);
        $session->set('captcha', $captcha);

        $img = imagecreate(65, 30);

        $font = $this->policeCaptcha;

        $textcolor = imagecolorallocate($img, 255, 255, 255); // First use define background color
        $textcolor = imagecolorallocate($img, 0, 0, 0);    // Second use define text color

        imagettftext($img, 23, 0, 3, 30, $textcolor, $font, $captcha);

        header('Content-type:image/jpeg');
        imagejpeg($img);
        imagedestroy($img);
    }
}
