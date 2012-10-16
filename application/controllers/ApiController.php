<?php

class Registration
{
	public function add()
	{
		return "lol";
	}
	
	public function uncheckedExams($apiKey)
	{
		// check if api key ist valid
		return 99;
	}
}

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
        
    }

    public function jsonAction()
    {
        // check for request
    	$service =new Zend_Json_Server();
    	$service->setClass("Registration");
    	echo $service->handle();
        
       // demo request: {"method":"uncheckedExams", "id":1, "Params":{"apiKey":"123456"}}
    }


}



