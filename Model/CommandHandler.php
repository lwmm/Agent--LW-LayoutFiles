<?php

namespace AgentLayoutFiles\Model;

class CommandHandler
{

    protected $config;
    protected $pictureExtensions;
    protected $allowedExtensions;

    public function __construct($config)
    {
        $this->config = $config;
        $this->pictureExtensions = array("jpg", "jpeg", "gif", "png", "bmp");
        $this->allowedExtensions = array("css", "js", "htm", "html", "flv", "phtml");
    }

    public function createLayoutFilesDirectoryIfNotExists()
    {
        if (!is_dir($this->config["path"]["resource"] . "layoutfiles/")) {
            $resource = \lw_directory::getInstance($this->config["path"]["resource"]);
            $resource->add("layoutfiles");

            $htaccess = fopen($this->config["path"]["resource"] . "layoutfiles/.htaccess", "w");
            fwrite($htaccess, '<FilesMatch "\.(php|PHP|php5|PHP5|pl|PL|phtml)$">'.PHP_EOL.'Order Allow,Deny'.PHP_EOL.'Deny from all'.PHP_EOL.'</FilesMatch>');
            fclose($htaccess);
        }
    }

    public function addDir($dir)
    {
        if(is_dir($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/")){
            return false;
        }
        $dir = preg_replace("/[^A-Za-z0-9_-]/", "", $dir);
        if (!is_dir($this->config["path"]["resource"] . "layoutfiles/" . $dir)) {
            $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/");
            $layoutFilesDir->add($dir);
        }
        return true;
    }

    public function deleteDir($dir)
    {
        $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/");
        $layoutFilesDir->delete();
    }

    public function addFile($fileDataArray, $dir)
    {
        $fileDataArray["name"] = preg_replace("/[^A-Z.a-z0-9_-]/", "", $fileDataArray["name"]);
        $filename = $fileDataArray["name"];
        $extension = \lw_io::getFileExtension($fileDataArray["name"]);

        $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/");
        $files = $layoutFilesDir->getDirectoryContents("file");

        foreach ($files as $file) {
            $name = $file->getName();

            if ($name == $fileDataArray["name"]) {
                $layoutFilesDir->deleteFile($file->getName());
            }
        }

        $layoutFilesDir->addFile($fileDataArray['tmp_name'], $filename);

        if (in_array($extension, $this->pictureExtensions)) {
            $layoutFilesDir->addFile($fileDataArray['tmp_name'], "thumbnail_" . $filename);

            list($width) = @getimagesize($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/" . "thumbnail_" . $fileDataArray["name"]);
            if ($width > 170) {
                $image = new \AgentLayoutFiles\Services\ImageResizer($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/" . "thumbnail_" . $fileDataArray["name"]);
                $image->setParams(170, 100);
                $image->resize();
                $image->saveImage();
            }
        }
    }

    public function deleteFiles($fileArray, $dir)
    {
        $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/");
        $files = $files = $layoutFilesDir->getDirectoryContents("file");

        foreach ($files as $file) {
            if (array_key_exists($file->getName(), $fileArray)) {

                $layoutFilesDir->deleteFile($file->getName());

                $extension = \lw_io::getFileExtension($file->getName());
                if (in_array($extension, $this->pictureExtensions)) {
                    $layoutFilesDir->deleteFile("thumbnail_" . $file->getName());
                }
            }
        }
    }

    public function renameFile($from, $to, $dir)
    {
        $extension = \lw_io::getFileExtension($from);

        $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/" . $dir . "/");

        $layoutFilesDir->renameFile($from, $to);

        if (in_array($extension, $this->pictureExtensions)) {
            $layoutFilesDir->renameFile("thumbnail_" . $from, "thumbnail_" . $to);
        }
    }

    public function renameDir($from, $to)
    {
        if(is_dir($this->config["path"]["resource"] . "layoutfiles/" . $to . "/")){
            return false;
        }
        $layoutFilesDir = \lw_directory::getInstance($this->config["path"]["resource"] . "layoutfiles/" . $from . "/");
        $layoutFilesDir->rename($to);
        
        return true;
    }

}
