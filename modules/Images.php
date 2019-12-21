<?php
namespace Mediashare\Modules;

use Mediashare\Entity\Url;

class Images {
    public $name = "Images";
    public $description = "Get all Images metadata & attributes";
    public $url; // Url with Headers & Body
    public $crawler; // Dom for crawl in webpage
    public $errors; // Output errors
    
    public function run() { 
        $images = [];
        foreach($this->crawler->filter('img') as $image) {
            if (!empty($image)) {
                $src = $this->setSource($image->getAttribute('src'));
                $alt = rtrim(ltrim($image->getAttribute('alt')));
                $filesize = (int) $this->getFileSize($src);
                $image = [
                    'src' => $src,
                    'alt' => $alt,
                    'filesize (kb)' => $filesize
                ];
                $images[] = $image;
            }
        }
        return $images;
    }

    /**
     * Reconctruct url from the <img src="/image.png">.
     *
     * @param string $url
     * @return string
     */
    public function setSource(string $url) {
        $url = rtrim(ltrim($url));
        if (!filter_var($url, FILTER_VALIDATE_URL)) { 
            $url = $this->url->getUrl().$url;
        }
        return $url;
    }

    public function getFileSize(string $url) {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_NOBODY, TRUE);

        $data = curl_exec($curl);
        $size = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($curl);
        return $size;
    }
}