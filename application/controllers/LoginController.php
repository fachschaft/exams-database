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

class LoginController extends Zend_Controller_Action
{

    private $_authManager = null;
    
    private $_filterManager;

    public function init()
    {
        $this->_authManager = new Application_Model_AuthManager();
        
        $this->_filterManager = new Application_Model_FilterManager();
        
		$this->_filterManager->setAllowedFileds(array(
				// default rule
				'*' =>array(
						'filter' 	=> array('StripTags'),
						'validator' => array()),

				// redirecting controller
				'rcontroller' =>array(
						'filter' 	=> array('StripTags'),
						'validator' => array()),
				
				// redirecting aktion
				'raction' => array(
						'filter' 	=> array('StripTags'),
						'validator' => array()),
				
				'username' => array(
						'filter' 	=> array('StripTags'),
						'validator' => array()),
				
				'password' => array(
						'filter' 	=> array('StripTags'),
						'validator' => array()),
				
					
		));

		$this->_filterManager->setFilterAndValidator();
		$this->_filterManager->applyFilterAndValidators($this->getRequest());
    }

    public function indexAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'view_login_form')) {
    		$this->_helper->Redirector->setGotoSimple ( 'index', 'default');
    	}
    	
		$request = $this->getRequest ();
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			// Get our form and validate it
			$form = $this->getLoginForm ();
			if (! $form->isValid ( $request->getPost () )) {
				$this->view->form = $form;
				return $this->render ( 'index' ); // re-render the login form
			}
			// Check if credentials provided are valid 
			$formdata = $form->getValues ();			
			if (!$this->_authManager->grantPermission($formdata)) {
				$form->setDescription ( 'Invalid credentials provided' );
				$this->view->form = $form;
				return $this->render ( 'index' ); // re-render the login form
			}
			
			else {
			$data = $this->getRequest ()->getParams ();
			
			// reconstruct the old parameters
			$data = $this->_authManager->popParameters($data);
			
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



