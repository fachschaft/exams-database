<?php
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
    	$service->setClass("Application_Model_API");
    	echo $service->handle();
        
       // demo request: {"method":"uncheckedExams", "id":1, "Params":{"apiKey":"123456"}}
    }


}



