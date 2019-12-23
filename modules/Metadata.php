<?php
namespace Mediashare\Modules;

/**
 * Metadata
 * Get all metadata from a webpage.
 */
class Metadata {    
    public $dom;
    public function run() { 
        // Get Title
        $results['title'] = $this->getTitle();
        // Get other Metadata
        $results['meta'] = $this->getOtherMeta();
        return $results;
    }

    private function getTitle() {
        $title = $this->dom->filterXpath("//title");
        if ($title->count()) {
            return $title->text();
        } else {
            $this->errors = [
                'type' => 'SEO',
                'message' => 'Title not found!',
                'url' => (string) $this->url,
            ];
        }
    }

    private function getOtherMeta() {
        $result = null;
        $metaBalises = $this->dom->filterXpath("//meta")->extract(array('name','property','content'));
        foreach ($metaBalises as $meta) {
            $type = null;
            if ($meta[0]) {
                $type = $meta[0];
            } elseif ($meta[1]) {
                $type = $meta[1];
            }
            
            if ($type) {
                $result[$type] = $meta[2];
            }
        }
        return $result;
    }
}