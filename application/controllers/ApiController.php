<?php
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */


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



