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

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
    	$errors = $this->_getParam('error_handler');
    	
    	// remove password at the error trace
    	$errors->request->setParam('password', '****');
    	

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
            	$priority = Zend_Log::NOTICE;
            	switch(get_class($errors->exception)) {
            		case'Custom_Exception_NotLoggedIn':
						$this->_forward('not-logged-in');
            			break;
            		case 'Custom_Exception_PermissionDenied':
            			$this->_forward('permission-denied');
            			break;
            			         				
           			default:
                		// application error            	
               			//$this->getResponse()->setHttpResponseCode(500);
                		$priority = Zend_Log::CRIT;
                		$this->view->message = 'Application error';
                		break;
            	}
        }
        
        // Log exception, if logger available
        $exception = $errors->exception;
        $trace = $exception->getTraceAsString();
        $log = $this->getLog();
        if ($log) { 
            $log->log($this->view->message . 
            		' : ' . $exception->getMessage() . ' triggered by ' . $this->getRequest()->getClientIp(), $priority, $errors->exception);
            if ($priority < 3)
            	$log->log('Stack Trace:'. "\n -------\n"   . $trace . "\n ------- ", $priority);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $errors->request->setParam('password', '****');
        
        $this->view->request   = $errors->request;

    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
	public function notLoggedInAction() {
		echo "Not logged in action called!";
		die();
	}
	
	public function permissionDeniedAction() {
		echo "Permission Denied action called!";
		die();
	}

}

