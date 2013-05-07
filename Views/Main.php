<?php

namespace AgentLayoutFiles\Views;

class Main
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function render($dir, $dirContent)
    {
        #print_r($dirContent);die();
        $baseUrl = substr(\AgentLayoutFiles\Services\Page::getUrl(), 0, strpos(\AgentLayoutFiles\Services\Page::getUrl(), "index.php"))."admin.php?obj=layoutfiles";

        $view = new \lw_view(dirname(__FILE__) . '/Templates/Main.phtml');

        $view->bootstrapCSS = $this->config["url"]["media"] . "bootstrap/css/bootstrap.min.css";
        $view->bootstrapJS = $this->config["url"]["media"] . "bootstrap/js/bootstrap.min.js";
        
        $view->dir = $dir;
        $view->dirContent = $dirContent;
        $view->baseUrl = $baseUrl;
        
        return $view->render();
    }

}