<?php

namespace AgentLayoutFiles\Views;

class Main
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function render($dir, $dirContent, $error)
    {
        #print_r($dirContent);die();
        $baseUrl = substr(\AgentLayoutFiles\Services\Page::getUrl(), 0, strpos(\AgentLayoutFiles\Services\Page::getUrl(), "index.php"))."admin.php?obj=layoutfiles";

        $view = new \lw_view(dirname(__FILE__) . '/Templates/Main.phtml');

        $view->bootstrapCSS = $this->config["url"]["media"] . "bootstrap/css/bootstrap.min.css";
        $view->bootstrapJS = $this->config["url"]["media"] . "bootstrap/js/bootstrap.min.js";
        $view->iconUrlFolder = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0440/folder.png";
        $view->iconUrlFolderClose = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0440/folder_go.png";
        $view->iconUrlEdit = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0700/pencil.png";
        $view->iconUrlImage = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0480/image.png";
        $view->iconUrlFile = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0640/page_white.png";
        $view->iconUrlRemove = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0300/cross.png";
        
        $view->dir = $dir;
        $view->dirContent = $dirContent;
        $view->baseUrl = $baseUrl;
        
        $view->error = $error;

        return $view->render();
    }

}