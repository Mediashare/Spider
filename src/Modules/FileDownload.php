<?php
namespace Spider\Modules;

class FileDownload {
    public $name = "FileDownload";
    public $description = "";
    public $config;
    public $webpage; // Headers & Body
    public $dom; // Dom for crawl in webpage
    public $variables = false; // Variables injected
    public $errors; // Output errors
    public $dir; // files direction

    public function run() {
        // File directory
        $domain = parse_url($this->webpage->getUrl())['host'];
        if (!\file_exists($this->config->reportsDir)):
            \mkdir($this->config->reportsDir);
        endif;
        $this->dir = rtrim($this->config->reportsDir, '/').'/'.$domain;
        if (!\file_exists($this->dir)):
            \mkdir($this->dir);
        endif;
        $this->dir .= '/files/';
        if (!\file_exists($this->dir)):
            \mkdir($this->dir);
        endif;

        $urls = $this->webpage->getLinks();
        $files = [];
        foreach ($urls as $url) {
            $file = $this->getFile($url);
            if ($file): 
                $files[] = $file;
            endif;
        }
        return [
            'dir' => $this->dir,
            'files' => $files,
        ];
    }
    public function getFile(string $url) {
        $url = preg_replace( '~\s+~', '%20', $url); // Url encoding
        if ($url) {
            // Check if url is file address
            $isFile = $this->isFile($url);
            if ($isFile):
            $download = $this->downloadFile($url);
            return $download;
            endif;
        }
    }

    public function isFile(string $url) {
        $header = get_headers($url, true);
        if (!empty($header["Content-Type"]) && !is_array($header["Content-Type"])):
            if (strpos($header["Content-Type"], 'text/html') === false):
                return true;
            endif;
        elseif (!empty($header[0]["Content-Type"]) && !is_array($header[0]["Content-Type"])):
            if (strpos($header[0]["Content-Type"], 'text/html') === false):
                return true;
            endif;
        else:
            return false;
        endif;
    }
    
    public function downloadFile(string $url) {
        $filename = urldecode(basename($url));
        $filename = preg_replace('~\s+~', ' ', $filename);
        $content = file_get_contents($url, $filename);
        if ($content):
            $file = fopen($this->dir.$filename, "w") or die("Unable to create file!");
            fwrite($file, $content);
            fclose($file);
            return $this->dir.$filename;
        else:
            return false;
        endif;
    }
}