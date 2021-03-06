<?php

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * YouTubeVideo
 */
class YouTubeVideo extends AbstractController
{
    /**
     * videoCleanUrlYT
     * Clean URL
     *
     * @param  mixed $videoUrl
     * @return void
     */
    public function videoCleanUrlYT($videoUrl)
    {
        if (!empty($videoUrl)) {
            $videoUrl = str_replace('youtu.be/', 'www.youtube.com/embed/', $videoUrl);
            $videoUrl = str_replace('www.youtube.com/watch?v=', 'www.youtube.com/embed/', $videoUrl);
        }

        return $videoUrl;
    }

    /**
     * videoIframeYT
     * iframe
     *
     * @param  mixed $videoUrl
     * @return void
     */
    public function videoIframeYT($videoUrl)
    {
        $videoIframe = '';

        if (!empty($videoUrl)) {
            $videoUrl = $this->videoCleanUrlYT($videoUrl);
            $videoIframe = '<iframe class="img-fluid" width="560" height="315" src="' . $videoUrl
                . '"  frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
        }

        return $videoIframe;
    }

    public function videoImgYT(string $videoUrl)
    {
        $videoEmbedImg        = '';
        if (!empty($videoUrl)) {
            $videoUrl            = $this->videoCleanUrlYT($videoUrl);
            $videoIdArray        = explode("/", '' . $videoUrl);
            // élément de l'URL après le dernier
            $videoId            = $videoIdArray[count($videoIdArray) - 1];
            //pass 0,1,2,3 for different sizes like 0.jpg, 1.jpg
            $videoEmbedImg     = 'https://i3.ytimg.com/vi/' . $videoId . '/hqdefault.jpg';
        }

        return '<img src="' . $videoEmbedImg . '"  class="img-fluid" width="500px" />';
    }
}
