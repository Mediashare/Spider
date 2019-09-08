<?php 
namespace App\Api;

class Docker {
    public function __construct() {
        $this->username = 'slote';
        $this->password = 'Timquand#1';
        $this->email = 'mediashare.supp@gmail.com';
        $this->unixSocket = "/var/run/docker.sock";
        $this->workingDir = "/home/webspider/";
        $this->volume = getcwd() . "/../var:/home/webspider/var";
        $this->image = "slote/webspider";
        $this->webspiderCommand = "bin/console webspider:run";
    }

    // ******************
    // Auth functions *
    // ******************
    public function auth() {
        $url = 'http:/v1.40/auth';
        $containers = $this->request($url, $method = "POST", $data = ['username' => $this->username, 'password' => $this->password]);
        return $containers;

    }

    // *************************
    // Request with Docker API *
    // *************************
    protected function request(string $url, string $method = "GET", array $data = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_UNIX_SOCKET_PATH, $this->unixSocket);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Registry-Auth: ' . base64_encode(json_encode(['username' => $this->username, 'password' => $this->password, 'email' => $this->email]))
        ]);
        
        if ($method === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $result = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
        curl_close($curl);
        
        $error = $this->catchError($url, $method, $data, $result);
        if ($error || $httpcode >= 400) {dd($error);}

        return $result;
    }

    protected function catchError(string $url, string $method, array $data = null, array $result = null) {
        if ($result && isset($result['message'])) {
            return [
                'url' => $url, 
                'method' => $method, 
                'data' => $data, 
                'result' => $result
            ];
        }
    }
}
