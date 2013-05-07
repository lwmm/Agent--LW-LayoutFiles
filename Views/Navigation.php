<?php

namespace AgentLayoutFiles\Views;

class Navigation
{
    protected $config;
    
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function render($dirs, $selectedDir)
    {
        $view = new \lw_view(dirname(__FILE__) . '/Templates/Navigation.phtml');
        
        $view->dirs = $dirs;
        $view->selectedDir = $selectedDir;
        $view->folderIconOpen = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0440/folder.png";
        $view->folderIcon = $this->config["url"]["media"]."pics/fatcow_icons/16x16_0440/folder_go.png";
                
        return $view->render();
    }
}