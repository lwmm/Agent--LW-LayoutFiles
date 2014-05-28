<?php

namespace AgentLayoutFiles\Controller;

class LayoutFilesController
{

    protected $config;
    protected $response;
    protected $request;

    public function __construct($config, $response, $request)
    {
        $this->config = $config;
        $this->response = $response;
        $this->request = $request;
    }

    public function execute()
    {
        $view = new \AgentLayoutFiles\Views\Main($this->config);
        $commandHandler = new \AgentLayoutFiles\Model\CommandHandler($this->config);
        $queryHandler = new \AgentLayoutFiles\Model\QueryHandler($this->config);
       
               
        $commandHandler->createLayoutFilesDirectoryIfNotExists();

        $error = false;
        $dir = "base";
        if ($this->request->getRaw("dir")) {
            if($queryHandler->isDirExisting($this->request->getRaw("dir"))){
                $dir = $this->request->getRaw("dir");
            }else{
                $dir = "base";
            }
        }

        
        if ($this->request->getAlnum("check") && $this->request->getAlnum("check") == "file") {
            $this->ajaxResponse($queryHandler);
        }
        

        if ($this->request->getAlnum("cmd") && !$this->request->getAlnum("error")) {

            $baseUrl = substr(\AgentLayoutFiles\Services\Page::getUrl(), 0, strpos(\AgentLayoutFiles\Services\Page::getUrl(), "index.php")) . "admin.php?obj=layoutfiles";


            switch ($this->request->getAlnum("cmd")) {
                case "addDir":
                    if(!$commandHandler->addDir($this->request->getAlnum("newDir"))){
                        $error = "&error=newdir&dirname=".$this->request->getAlnum("newDir");
                    }
                    break;
                case "addFile":
                    $commandHandler->addFile($this->request->getFileData("newFile"), $this->request->getAlnum("dir"));
                    \AgentLayoutFiles\Services\Page::reload($baseUrl . "&dir=" . $this->request->getAlnum("dir"));
                    break;
                case "deleteDir":
                    $commandHandler->deleteDir($this->request->getAlnum("dir"));
                    break;
                case "deleteFiles":
                    $commandHandler->deleteFiles($this->request->getRaw("delete"), $this->request->getAlnum("dir"));
                    \AgentLayoutFiles\Services\Page::reload($baseUrl . "&dir=" . $this->request->getAlnum("dir"));
                    break;
                case "renameFile":
                    $commandHandler->renameFile($this->request->getRaw("from"), $this->request->getRaw("to"), $this->request->getAlnum("dir"));
                    \AgentLayoutFiles\Services\Page::reload($baseUrl . "&dir=" . $this->request->getAlnum("dir"));
                    break;
                case "renameDir":
                    if(!$commandHandler->renameDir($this->request->getRaw("from"), $this->request->getRaw("to"))){                        
                        $error = "&error=renamedir&from=".$this->request->getRaw("from")."&to=".$this->request->getRaw("to");
                    }
                    break;
            }
            \AgentLayoutFiles\Services\Page::reload($baseUrl.$error);
        }

        if ($this->request->getAlnum("error")) {
            
            switch ($this->request->getAlnum("error")) {
                case "renamedir":
                    $error["renameDir"] = array("from" => $this->request->getRaw("from"),"to" => $this->request->getRaw("to"));
                    break;
                case "newdir":
                    $error["newDir"] = array("dirname" => $this->request->getAlnum("dirname"));
                    break;
            }
        }

        if ($dir == "base") {
            $dirContent = $queryHandler->getAllDirectoriesDetailed();
        }
        else {
            $dirContent = $queryHandler->getFiles($dir);
        }

        $this->response->setOutputByKey("AgentLayoutFiles", $view->render($dir, $dirContent, $error));
    }

    private function ajaxResponse($queryHandler)
    {
        if (!$queryHandler->checkUploadFileExtenstion($this->request->getRaw("filename"))) {
            exit("NotAllowedExtension");
        }
        elseif ($queryHandler->existsFilename($this->request->getRaw("filename"), $this->request->getAlnum("dir"))){
            exit("FilenameExisting");
        }else{
            exit("NothingWrong");
        }
    }
}