<?php

class agent_layoutfiles extends lw_agent
{

    protected $config;
    protected $request;
    protected $response;

    public function __construct()
    {
        parent::__construct();
        $this->config = $this->conf;
        $this->className = "agent_layoutfiles";
        $this->adminSurfacePath = $this->config['path']['agents'] . "adminSurface/templates/";

        $usage = new lw_usage($this->className, "0");
        $this->secondaryUser = $usage->executeUsage();

        include_once(dirname(__FILE__) . '/Services/Autoloader.php');
        $autoloader = new \AgentLayoutFiles\Services\Autoloader();
    }

    protected function showEdit()
    {
        $response = new \AgentLayoutFiles\Services\Response();
        $controller = new \AgentLayoutFiles\Controller\LayoutFilesController($this->config, $response, $this->request);
        $controller->execute();
        return $response->getOutputByKey("AgentLayoutFiles");
    }

    protected function buildNav()
    {
        $queryHandler = new \AgentLayoutFiles\Model\QueryHandler($this->config);
        $view = new \AgentLayoutFiles\Views\Navigation($this->config);
        return $view->render($queryHandler->getAllDirectories(),$this->request->getRaw("dir"));
    }

    protected function deleteAllowed()
    {
        return true;
    }

}