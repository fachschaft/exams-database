<?php

class LoginController extends Zend_Controller_Action
{

    private $_authManager = null;

    public function init()
    {
        $this->_authManager = new Application_Model_AuthManager();
    }

    public function indexAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'view_login_form')) {
    		$this->_helper->Redirector->setGotoSimple ( 'index', 'default');
    	}
    	
		$authmanager = new Application_Model_AuthManager();
		$request = $this->getRequest ();
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			// Get our form and validate it
			$form = $this->getLoginForm ();
			if (! $form->isValid ( $request->getPost () )) {
				$this->view->form = $form;
				return $this->render ( 'login' ); // re-render the login form
			}
			// Check if credentials provided are valid 
			$formdata = $form->getValues ();			
			if (!$authmanager->grantPermission($formdata)) {
				$form->setDescription ( 'Invalid credentials provided' );
				$this->view->form = $form;
				return $this->render ( 'login' ); // re-render the login form
			}
			
			else {
			$data = $this->getRequest ()->getParams ();
			
			// reconstruct the old parameters
			$data = $authmanager->popParameters($data);
			
			$this->_helper->Redirector->setGotoSimple ( $data ['action'], $data ['controller'], null, $data );
			}
		}
		
		$this->view->form = $this->getLoginForm ();
    }
    
    public function logoutAction()
    {
		Application_Model_AuthManager::clearIdentity();
		$this->_helper->redirector ( 'index', 'default' ); // back to login page
    }

    private function getLoginForm()
    {
    	return new Application_Form_AdminLogin ( array (
    			'action' => '',
    			'method' => 'post'
    	) );
    }

    


}



