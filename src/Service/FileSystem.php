<?php
namespace App\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem as fs;

class FileSystem
{
   public function __construct() {
      $this->filesystem = new fs();
   }

   public function createJsonFile($json, string $fileDir) {
      $filename = basename($fileDir);
      $path = dirname($fileDir);
      // Create folder
      if (!$this->filesystem->exists($path)) {
         try {
            $this->filesystem->mkdir($path);
         } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
         }
      }
      if ($this->filesystem->exists($fileDir)) {
         $this->filesystem->remove($fileDir);
      }
      // Create File
      $this->filesystem->dumpFile($fileDir, $json);
   }

   public function remove(string $fileDir) {
      if ($this->filesystem->exists($fileDir)) {
         try {
            $this->filesystem->remove($fileDir);
         } catch (IOExceptionInterface $exception) {
            echo "An error occurred while remove ".$fileDir;
         }
      }
      return true;
   }

   public function exist(string $fileDir) {
      return $this->filesystem->exists($fileDir);
   }

   public function getFile(string $fileDir) {
      if (!$this->exist($fileDir)) {return false;}
      return \file_get_contents($fileDir);
   }
}
