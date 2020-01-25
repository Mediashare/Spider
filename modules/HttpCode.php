<?php
namespace Mediashare\Modules;

Class HttpCode
{
    public $url;
    public function run() {
        $httpCode = $this->getHttpCode($this->url);
        $result = $this->checkResponse($httpCode);
        return $result;
    }
    public function getHttpCode(string $url): string {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // Error(s)
        $error = curl_error($curl);
        $errno = curl_errno($curl);
        curl_close($curl);

        if (0 !== $errno) {
            throw new \RuntimeException($error, $errno);
        }

        return $httpCode;
    } 
    public function checkResponse(string $httpCode): array {
        if (substr($httpCode, 0, 1) == 1 || substr($httpCode, 0, 1) == 2):
            $result = [
                'http_code' => $httpCode,
                'status' => 'success'
            ];
        elseif (substr($httpCode, 0, 1) == 3):
            $result = [
                'http_code' => $httpCode,
                'status' => 'redirection'
            ];
        elseif (substr($httpCode, 0, 1) == 4):
            $result = [
                'http_code' => $httpCode,
                'status' => 'client error'
            ];
            $this->errors = $result;
        elseif (substr($httpCode, 0, 1) == 5):
            $result = [
                'http_code' => $httpCode,
                'status' => 'server error'
            ];
            $this->errors = $result;
        endif;
        return $result;
    }
}
