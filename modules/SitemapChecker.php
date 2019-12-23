<?php
namespace Mediashare\Modules;

class SitemapChecker {    
    public $body;
    public function run() {
        $body = $this->body;
        $urls = $this->getUrls($body);
        $results = $this->getHttpResponse($urls);
        return $results;
    }

    private function getUrls($body) {
        $matches = [];
        $searchUrl = preg_match_all('/<loc>(.*?)\<\/loc>/s', $body, $matches);
        $urls = [];

        foreach ((array) $matches[0] as $url) {
            $url = str_replace('<loc>', '', $url);
            $url = str_replace('</loc>', '', $url);

            if (!filter_var($url, FILTER_VALIDATE_URL))
            {$this->errors[] = "Invalid url in sitemap: " . $url;} // Catch Invalid Url
            else {$urls[] = $url;}
        }
        return $urls;
    }

    private function getHttpResponse(array $urls) {
        // echo "\n";
        $total = count($urls);
        $responses = [];
        foreach ($urls as $counter => $url) {
            $response = $this->request($url);
            if ($response['httpCode'] === 0) {sleep(5);$response = $this->request($url);} // Curl fail, retry!
            if ($response['httpCode'] === 0) {$response['httpCode'] = 'Curl Fail Request';} // Curl's request is still fail!

            // Save Result
            // echo ($counter + 1) . '/' . $total . ' | (' . $response['httpCode'] . ') ' . $url . "\n";
            $responses[$response['httpCode']][] = [
                'url' => $url,
                'httpCode' => $response['httpCode'],
                'output' => $response['output'],
            ];
        }
        return $responses;
    }

    private function request(string $url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($curl, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_TIMEOUT,10);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13');
        $output = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            'output' => $output,
            'httpCode' => $httpCode
        ];
    }
}