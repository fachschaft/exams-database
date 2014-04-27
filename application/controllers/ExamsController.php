<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.1
 * @since		1.0
 * @todo		-
 */

class ExamsController extends Zend_Controller_Action {
	
	private $_authManager;
	
	private $_filterManager;
	
	public function init() {
		
		//Initialize the auth manager to enable acl
		$this->_authManager = new Application_Model_AuthManager();
		
		// Initialize the filter to secure post and get variables
		$this->_filterManager = new Application_Model_FilterManager();
		
		// define all allowed fields
		$this->_filterManager->setAllowedFileds(array(
				// default rule
				'*' =>array(
						'filter' 	=> array('StripTags'),//'HtmlEntities'),
						'validator' => array()),

				// ??
				'id' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				
				// Group select to degree
				'group' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				
				// Degree select to detail search mask (courses)
				'degree' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				
				// Search with regualr parms
				'course' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),				
				'lecturer' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'semester' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'examType' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				
				// Search with quicksearch query string
				'request' =>array(
						'filter' 	=> array('StripTags'),
						'validator' => array()),

				/*'submit' =>array(
						'filter' 	=> array('Alnum'),
						'validator' => array()),*/
				
				// Quicksearch
				'_query' =>array(
						'filter' 	=> array('StripTags'),
						'validator' => array()),
						
				// Quicksearch Query (jQuery - ajax)
				'term' =>array(
						'filter' 	=> array('StripTags'),
						'validator' => array()),
						
				// Reporting
				'_reason' =>array(
						'filter' 	=> array(new Zend_Filter_Alnum(array('allowwhitespace' => true))),
						'validator' => array(new Zend_Validate_Alnum(array('allowwhitespace' => true)))),
				//Download from admin area
				'admin' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'nolog' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
		));

		$this->_filterManager->setFilterAndValidator();
		$this->_filterManager->applyFilterAndValidators($this->getRequest());
	}
	
	
	
	
	// Scrub entries from $_POST[] that are not needed
	private function scrubPost(array $scrubEntries, array $scrubIfAllSelected = NULL) {
		$_post = $this->getRequest ()->getParams ();
		foreach ( $scrubEntries as $entry ) {
			if (isset ( $_post [$entry] ))
				unset ( $_post [$entry] );
		}
		if ($scrubIfAllSelected != NULL) {
			foreach ( $scrubIfAllSelected as $entry ) {
				if (isset ( $_post [$entry] ) && in_array ( - 1, $_post [$entry] ))
					unset ( $_post [$entry] );
			}
		}
		return $_post;
	}
	
	public function indexAction() {

		$this->_helper->redirector ( 'quick-search' );
	}
	
	public function groupsAction() {
		$form = new Application_Form_DegreeGroups ();
		$this->view->form = $form;
		
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $this->getRequest ()->getParams() )) {
				$post = $this->scrubPost(array('submit'));
				return $this->_helper->Redirector->setGotoSimple ( 'degrees', null, null, $post );
			}
		}	
	}
	
public function degreesAction() {
	$form = new Application_Form_ExamDegrees();
	if(!$this->_authManager->isAllowed(null, 'search')) 
		throw new Custom_Exception_PermissionDenied("Permission Denied");	
		
    if ($this->getRequest()->isPost()) {
            $form->setMultiOptions($this->getRequest()->group);
            $form->setGroup($this->getRequest()->group);
            
            if($form->isValid($this->getRequest()->getParams())) {
            	$post = $this->scrubPost(array('submit', 'group'));
                return $this->_helper->Redirector->setGotoSimple('courses', null, null, $post);
            }
        }
        
        if(isset($this->getRequest()->group)) {

            $form->setMultiOptions($this->getRequest()->group);
            $form->setGroup($this->getRequest()->group);
            $this->view->form = $form;
        } else {
            return $this->_helper->redirector('groups');
        }
		
    }

    public function coursesAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'search'))
    		throw new Custom_Exception_PermissionDenied("Permission Denied");
        $form = new Application_Form_ExamCourses();

        // go back to group
        if(!isset($this->getRequest()->degree)) {
            return $this->_helper->redirector('groups');
        } else {
            //TODO(aritas1): check if the degree is valid (db check)
        }
        
        //setup the form
        $form->setCourseOptions($this->getRequest()->degree);
        $form->setLecturerOptions($this->getRequest()->degree);
        $form->setDegree($this->getRequest()->degree);
        
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getParams())) {
            	$post = $this->scrubPost(array('submit'), array('lecturer', 'course', 'semester', 'examType'));
  
                 return $this->_helper->Redirector->setGotoSimple('search', null, null, $post);

            } else {
                $this->view->form = $form;
            }
        }
        
        $this->view->form = $form;
    }

    public function searchAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'search'))
    		throw new Custom_Exception_PermissionDenied("Permission Denied");
        $request = $this->getRequest();
        $this->view->exams = array();
        if(isset($this->getRequest()->request)) {
        	$index = new Application_Model_ExamSearch();
        	$this->view->exams = $index->searchExams($this->getRequest()->request);
        } else {
	        // go back to degree
	        if(!isset($this->getRequest()->degree)) {
	            return $this->_helper->redirector('index');
	        } else {
	            //TODO(aritas1): check if the degree is valid (db check)
	            // check also if the combination of degree / group is valid
	        }
	        
	        if(!isset($this->getRequest()->course)) {
	            $this->getRequest()->setParam('course', -1);
	        }
	        
	        if(!isset($this->getRequest()->lecturer)) {
	            $this->getRequest()->setParam('lecturer', -1);
	        }
	        
	        if(!isset($this->getRequest()->semester)) {
	            $this->getRequest()->setParam('semester', -1);
	        }
	        
	        if(!isset($this->getRequest()->examType)) {
	            $this->getRequest()->setParam('examType', -1);
	        }
	        
	        $exams = new Application_Model_ExamMapper();
	        $this->view->exams = $exams->fetchQuick(
	                        $this->getRequest()->course,
	                        $this->getRequest()->lecturer,
	                        $this->getRequest()->semester,
	                        $this->getRequest()->examType, 
	                        $this->getRequest()->degree,
							array(Application_Model_ExamStatus::PublicExam, Application_Model_ExamStatus::Reported)
	                        );

	     //   
	    }
	    //
    }
	
	public function downloadAction() {
		
		if (! Application_Model_AuthManager::hasIdentity()) {
			$ip = array (
					'ip' => $this->getRequest ()->getClientIp ()
			);
			$this->_authManager->grantPermission($ip);
		}
		
		if(!$this->_authManager->isAllowed(null, 'download'))
			throw new Custom_Exception_NotLoggedIn("Sorry, your not logged in");
		if (isset ( $this->getRequest ()->id )) {
			// For anonymous Users, check if the user is allowed to download
			// files based on
					$documentMapper = new Application_Model_DocumentMapper ();
					$document = $documentMapper->fetch ( $this->getRequest ()->id );
					
					if ($document->getId() == NULL)
							throw new Zend_Controller_Action_Exception("No files found", 404);
					
					if ($document->exam->status->id == Application_Model_ExamStatus::PublicExam || $document->exam->status->id == Application_Model_ExamStatus::Reported)
						$fileId = $this->getRequest ()->id;
					else
						throw new Custom_Exception_PermissionDenied("You tryed to call a non public document");
		 	$fileId = $this->getRequest ()->id;
		 }
		if (isset ( $this->getRequest ()->admin )) {
				$fileId = $this->getRequest ()->admin;
		}
		
		if (!isset ($fileId))
			throw new Exception('No File ID set, something whent very wrong!');
		
		// check if the downloas was calld from an admin and set the reviews state of the document
		$count = true;
		// if the nolog flag is set, dont count this as a review
		if (isset ($this->getRequest ()->admin) && !isset ( $this->getRequest ()->nolog)) {
			$maper = new Application_Model_DocumentMapper();
			$maper->updateReviewState($fileId);
			$count = false;
		}
		
		// if the nolog flag is set, dont count this download
		if (isset ( $this->getRequest ()->nolog)) {
			$count = false;
		}
		
		// If all conditions are met, send the User the file he requested for Download.
		$filemanager = new Application_Model_ExamFileManager ();
		$filemanager->downloadDocuments ( $fileId , $count);
		
		// This exit() is important as php will output a lot of html instead of
		// just the file contents if it is missing.
		exit ();
	}
	
	public function reportAction() {
		if(!$this->_authManager->isAllowed(null, 'report'))
			throw new Custom_Exception_PermissionDenied("Permission Denied");
		$examid = $this->getRequest ()->id;
		$form = new Application_Form_ExamReport ();
		$form->setAction ( $examid );
		if ($this->_request->isPost () && $form->isValid($this->_request->getParams ())) {
			$formData = $this->_request->getParams ();
			$examMapper = new Application_Model_ExamMapper ();
			// TODO check escaping as a get variable is passed to mysql here!!!! Maybe find a nicer way of doing this when it's less late.		
			$examMapper->updateExamStatusToReported ( $examid, $formData['_reason']);
			echo "Your report was submitted. Thank you for your help.";
			$form->reset();
		}
		$this->view->form = $form;
	}

    public function quickSearchAction()
    { 	
    	if(!$this->_authManager->isAllowed(null, 'quick_search'))
    		throw new Custom_Exception_PermissionDenied("Permission Denied");
    	$found = false;
    	$form = new Application_Form_ExamQuickSearch();
    	if ($this->_request->isPost()) {
    		$formData = $this->_request->getParams();
    		$formData['_query'] = html_entity_decode($formData['_query']);
    		//echo $formData['_query'];
    		if ($form->isValid($formData)) {
	       		$index = new Application_Model_ExamSearch();
	       		$found = $index->searchExists($formData['_query']);
	    		if(!$found) {
	    			$form->getElement("_query")->addError("no results found!");
	    		}
    		} else {
    			//var_dump($form->getErrors());
    		}
    	}
    	$this->view->form = $form;
    	
    	// draw the form first, so ists possible to use the back key from your browser to modify the search
    	if($found) {
    		$data['request'] = $formData['_query'];
    		return $this->_helper->Redirector->setGotoSimple( 'search', null, null, $data );
    	}
    	
    }
    
    public function quickSearchQueryAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'quick_search'))
    		throw new Custom_Exception_PermissionDenied("Permission Denied");
    	
    	$eqsq = new Application_Model_ExamQuickSearchQuery();
    	
    	$results = $eqsq->getResults($this->_getParam('term'));
    	$this->_helper->json(array_values($results));
    	
    	
    	 
    }
}
