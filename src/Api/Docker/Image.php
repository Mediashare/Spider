<?php 
namespace App\Api\Docker;
use App\Api\Docker;

class Image extends Docker {

    // ******************
    // Images functions *
    // ******************

    public function createImage(string $name, string $tag = null) {
        $query = "fromImage=" . $name;
        if ($tag) {$query .= "&tag=" . $tag;}
        $url = 'http:/v1.40/images/create?' . $query;
        $containers = $this->request($url, $method = "POST", $data = null);
        return $containers;
    }
}
