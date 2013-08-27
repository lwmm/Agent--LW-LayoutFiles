<?php

namespace AgentLayoutFiles\Model;

class QueryHandler
{

    protected $config;
    protected $pictureExtensions;
    protected $allowedExtensions;

    public function __construct($config)
    {
        $this->config = $config;
        $this->pictureExtensions = array("jpg", "jpeg", "gif", "png", "bmp");
        $this->allowedExtensions = array("css", "js", "htm", "html", "flv", "eot", "svg", "ttf", "woff");
    }

    public function getAllDirectories()
    {
        $preparedArray = array();
        $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/");
        $dirs = $layoutFilesDir->getDirectoryContents("dir");

        foreach ($dirs as $dir) {
            $preparedArray[] = array("name" => str_replace("/", "", $dir->getName()));
        }

        return $preparedArray;
    }

    public function getAllDirectoriesDetailed()
    {
        $preparedArray = array();
        $dirs = $this->getAllDirectories();

        foreach ($dirs as $dir) {
            $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/" . $dir["name"] . "/");
            $files = $layoutFilesDir->getDirectoryContents("files");
            $i = 0;
            $size = 0;
            foreach ($files as $file) {
                if (!strstr($file->getName(), "thumbnail_")) {
                    $i++;
                }
                $size += $file->getSize(true);
            }

            $preparedArray[$dir["name"]]["fileCount"] = $i;
            $preparedArray[$dir["name"]]["completeFileSize"] = $this->humanFileSize($size);
        }

        return $preparedArray;
    }

    public function getFiles($dir)
    {
        $preparedArray = array();

        $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/");
        $files = $layoutFilesDir->getDirectoryContents("files");

        foreach ($files as $file) {
            if (!strstr($file->getName(), "thumbnail_")) {

                $preparedArray[$file->getName()]["size"] = $file->getSize();
                $preparedArray[$file->getName()]["date"] = $file->getDate();
                $preparedArray[$file->getName()]["url"] = str_replace($this->config["path"]["resource"], $this->config["url"]["resource"], $file->getPath() . $file->getName());

                if (in_array($file->getExtension(), $this->pictureExtensions)) {
                    $preparedArray[$file->getName()]["type"] = "picture";
                }
                else {
                    $preparedArray[$file->getName()]["type"] = "file";
                }
            }
        }

        return $preparedArray;
    }

    private function humanFileSize($size)
    {
        if ($size == 0) {
            return("0 Bytes");
        }
        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
    }
    
    public function checkUploadFileExtenstion($filename)
    {
        $filename = preg_replace("/[^A-Z.a-z0-9_-]/", "", $filename);
        $extension = \lw_io::getFileExtension($filename);
        
        if(in_array($extension, $this->allowedExtensions) || in_array($extension, $this->pictureExtensions)) {
            return true;
        }
        
        return false;
    }
    public function existsFilename($filename, $dir)
    {
        $filename = preg_replace("/[^A-Z.a-z0-9_-]/", "", $filename);
        $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/");
        $files = $layoutFilesDir->getDirectoryContents("file");
        foreach ($files as $file) {
            if ($file->getName() == $filename) {
                return true;
            }
        }
        return false;
    }

    public function isDirExisting($dir)
    {
        return is_dir($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/");
    }
}
