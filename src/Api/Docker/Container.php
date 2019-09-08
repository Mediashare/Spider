<?php 
namespace App\Api\Docker;
use App\Api\Docker;

class Container extends Docker {
    // *********************
    // Container functions *
    // *********************

    /**
     * Get all docker containers
     * @return array $filters
     * @return array $containers
     */
    public function getContainers(array $filters = ['all' => 1, 'size' => 1]) {
        $query = "";
        foreach ($filters as $key => $value) {$query .= $key.'='.$value;}
        $url = 'http:/v1.40/containers/json?' . $query;
        $containers = $this->request($url, $method = "GET", $data = null);
        return $containers;
    }

    /**
     * Get docker container by id or name
     * @return array $filters
     * @return array $container
     */
    public function getContainer(string $id) {
        $url = 'http:/v1.40/containers/' . rtrim($id, '/') . '/json';
        $container = $this->request($url, $method = "GET", $data = null);
        return $container;
    }
    
    /**
     * Create docker container with command preset
     * @param string $name
     * @param string $arguments
     * @return array $container[Id, Warnings]
     */
    public function create(string $name) {
        $url = 'http:/v1.40/containers/create';
        if ($name) {$url .= '?name=' . $name;}
        $data = [
            'Image' => $this->image,
            'WorkingDir' => $this->workingDir,
            'Cmd' => ['echo', 'hello world'],
            // 'Cmd' => $this->command." ".$arguments,
            'Volumes' => $this->volume,
        ];
        $container = $this->request($url, $method = "POST", $data);
        return $container;
    }
    
    /**
     * Start docker container by id
     *
     * @param string $id
     * @return bool
     */
    public function start(string $id) {
        $url = 'http:/v1.40/containers/' . rtrim($id, '/') . '/start';
        $result = $this->request($url, $method = "POST", $data = null);
        return $result;
    }
    
    /**
     * Stop docker container by id
     *
     * @param string $id
     * @return array $result
     */
    public function stop(string $id) {
        $url = 'http:/v1.40/containers/' . rtrim($id, '/') . '/stop';
        $result = $this->request($url, $method = "POST", $data = null);
        return $result;
    }
    
    /**
     * Freeze docker container by id
     *
     * @param string $id
     * @return array $result[StatusCode]
     */
    public function wait(string $id) {
        $url = 'http:/v1.40/containers/' . rtrim($id, '/') . '/wait';
        $result = $this->request($url, $method = "POST", $data = null);
        return $result;
    }

    /**
     * Output logs from docker container by id
     *
     * @param string $id
     * @return string $output
     */
    public function logs(string $id) {
        $url = 'http:/v1.40/containers/' . rtrim($id, '/') . '/logs?stdout=1';
        $output = $this->request($url, $method = "GET", $data = null);
        return $output;
    }
}
