<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.1
 * @since		1.1
 * @todo		-
 */

class PadController extends Zend_Controller_Action
{
	private $_authManager;

    public function init()
    {	
    	// cron action shoud be callable for anybody
    	if($this->getRequest()->getActionName() != "cron") {
	    	$this->_authManager = new Application_Model_AuthManager();
			// check if a login exists for admin controller
			if ((!$this->_authManager->isAllowed(null, 'view_admin_interface'))) {
				$data = $this->getRequest ()->getParams ();
				// save the old controller and action to redirect the user after the login
				$authmanager = new Application_Model_AuthManager ();
				$data = $authmanager->pushParameters ( $data );
				
				$this->_helper->Redirector->setGotoSimple ( 'index', 'login', null, $data );
			
			}
    	}
		
		//$this->view->jQuery()->enable();
		//$this->view->jQuery()->uiEnable();
		
		//
    }

    public function indexAction()
    {
		// action body
    }
    
    
    public function overviewAction()
    {
    	$request = $this->getRequest ();
    	if (isset ( $request->do ) && isset ( $request->id )) {
    		$do = $request->do;
    		$id = $request->id;
    		
    		$pm = new Application_Model_PadMapper();
    			
    		switch ($do) {
    			case "delete_pad" :
    				if(!$this->_authManager->isAllowed(null, 'pad_delete')) {
    					echo "You can't do that!";
    					break;
    				}
    				$pm->deletePad($id);
    				$this->_helper->Redirector->setGotoSimple ( 'overview' );
    				break;

    			default :
    				throw new Zend_Exception ( "Sorry, the do action (\"" . $do . "\") is not implemented." );
    				break;
    		}
    	}
    	
    	
    	// action body
    	$pm = new Application_Model_PadMapper();
    	
    	$pads =  $pm->fetchAll(true);   	
    	$this->view->pads = $pads;
    }
    
    public function ensureAction()
    {
    	$request = $this->getRequest ();
    	if (isset ( $request->do ) && isset ( $request->id )) {
    		$do = $request->do;
    		$id = $request->id;
    
    		 
    		switch ($do) {
    			case "delete_pad" :
    				$this->view->action = 'delete_pad';
    				$this->view->id = $id;
    				break;
 					
    		}
    
    	}
    	//
    }
    
    public function maintenanceAction()
    {
    	$request = $this->getRequest ();
    	if (isset ( $request->do )) {
    		$do = $request->do;
    		 
    		switch ($do) {
    			case "cronFull" :
    
    				if($this->_authManager->isAllowed(null, 'pad_maintenance')) {
    					$pm = new Application_Model_PadMapper();
    					$pm->padCronFull();
    					echo "padCronFull executed";
    				} else { echo "Sorry, not allowed!"; }
    				break;
    		}
    	}
    }
    
    public function cronAction()
    {
    	$pm = new Application_Model_PadMapper();
    	$request = $this->getRequest ();
    	if (isset ( $request->key )) {
    		if($request->key != $pm->_pad_cronkey) {
    			echo "ERROR<br>";
    			echo "key incorrect!";
    			exit();
    		}
    	} else {
    		echo "ERROR<br>";
    		echo "no key given!";
    		exit();
    	}
    	
    	$pm->padCronPartialDays();
    	
    	
    	echo "OK";
    	exit();
    }

}

