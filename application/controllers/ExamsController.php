<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class ExamsController extends Zend_Controller_Action {
	
	private $_profiler;
	
	private $_filterManager;
	
	public function init() {
		
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
						
				// Reporting
				'_reason' =>array(
						'filter' 	=> array('Alnum'),
						'validator' => array('Alnum')),
						
				// Upload step 2
				'exam' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'step' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'type' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'subType' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'degree_exam' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'university' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'comment' =>array(
						'filter' 	=> array('StripTags', 'StringTrim'),
						'validator' => array()),
				'autor' =>array(
						'filter' 	=> array('StripTags', 'StringTrim'),
						'validator' => array()),	
						
				// Upload step 3		
				'examId' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'files' =>array(
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

		$this->_helper->redirector ( 'groups' );
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
		$authmanager = new Application_Model_AuthManager ();
		if (isset ( $this->getRequest ()->id )) {
			// For anonymous Users, check if the user is allowed to download
			// files based on IP
			if (! Application_Model_AuthManager::hasIdentity()) {
				$ip = array (
						'ip' => $this->getRequest ()->getClientIp () 
				);
				if ($authmanager->grantPermission ( $ip )) {
					// If user is allowed to download, get the fileid for the
					// download
					$documentMapper = new Application_Model_DocumentMapper ();
					$document = $documentMapper->fetch ( $this->getRequest ()->id );
					
					if ($document->exam->status->id == Application_Model_ExamStatus::PublicExam || $document->exam->status->id == Application_Model_ExamStatus::Reported)
						$fileId = $this->getRequest ()->id;
					else
						throw new Exception ( "Sorry, you are not allowed to download that file", 500 );
				} 
				else {
				$data = $this->getRequest ()->getParams ();
				// save the old controller and action to redirect the user after
				// the login
				$data = $authmanager->pushParameters ( $data );
				die("Application");
				$this->_helper->redirector('login', 'exams-admin', NULL, $data);
			}
			
			}
		 if (Application_Model_AuthManager::hasIdentity())
		 	$fileId = $this->getRequest ()->id;
		 }
		if (isset ( $this->getRequest ()->admin )) {
			// ToDo: check for admin state
			
			// check if a login exists for admin controller
			if (Application_Model_AuthManager::hasIdentity ()) {
				// If user is logged in, get the fileid for the download
				$fileId = $this->getRequest ()->admin;
			} else {
				$data = $this->getRequest ()->getParams ();
				// save the old controller and action to redirect the user after
				// the login
				$data = $authmanager->pushParameters ( $data );
				
				$this->_helper->redirector('login', 'exams-admin', NULL, $data);
			}
		} 
		if (!isset ($fileId))
			throw new Exception('You are not allowed to download files!');
			
		// If all conditions are met, send the User the file he requested for Download.
		$filemanager = new Application_Model_ExamFileManager ();
		$filemanager->downloadDocuments ( $fileId );
		
		// This exit() is important as php will output a lot of html instead of
		// just the file contents if it is missing.
		exit ();
	}
	
	public function uploadAction() {
		
		$form = null;
		$step = 1;
		
		if (isset ( $this->getRequest ()->degree )) {
			$step = 2;
			$form = new Application_Form_UploadDetail ();
			$form->setCourseOptions (new Application_Model_Degree(array('id'=>$this->getRequest ()->degree )));
			$form->setLecturerOptions (new Application_Model_Degree(array('id'=>$this->getRequest ()->degree )));
			$form->setDegree ( $this->getRequest ()->degree );
		}
		
		if (isset ( $this->getRequest ()->exam )) {
			$step = 3;
			$form = new Application_Form_UploadFile ();
			$form->setExamId ( $this->getRequest ()->exam );
			
			$config = Zend_Registry::get ( 'examDBConfig' );
			$this->view->exam = $this->getRequest ()->exam;
			$this->view->files = 3;
			
			if (isset ( $this->getRequest ()->files )) {
				if ($this->getRequest ()->files + 3 > $config ['max_upload_files']) {
					$this->view->files = $config ['max_upload_files'];
				} else {
					$this->view->files = $this->getRequest ()->files + 3;
				}
			}
		
		}
		
		if ($this->getRequest ()->isPost () || $step == 3) {
			
			if (isset ( $this->getRequest ()->step )) {
				$step = $this->getRequest ()->step;
			}
			
			switch ($step) {
				case 1 :
					$form = new Application_Form_UploadDegrees ();
					if ($form->isValid ( $this->getRequest()->getParams() )) {
						$post = $this->getRequest ()->getParams ();
						unset ( $post ['submit'] );
						unset ( $post ['step'] );
						$this->_helper->Redirector->setGotoSimple ( 'upload', null, null, $post );
					}
					break;
				case 2 :
					$form = new Application_Form_UploadDetail ();
					$form->setCourseOptions ( new Application_Model_Degree(array('id'=>$this->getRequest ()->degree)) );
					$form->setLecturerOptions ( new Application_Model_Degree(array('id'=>$this->getRequest ()->degree)) );
					$form->setDegree ( $this->getRequest ()->degree );		
					
					//Note: SECURETY, we use _inputUnescaped for Validation!
					//		This only includes FILTERD and VALID defined parms from the init()
					//		Ensure all passed Strings are secure
					if ($form->isValid ( $this->_filterManager->getInputUnescaped())) {
						$post = $this->getRequest ()->getParams (); //for insert we use escaped strings
						
						// insert the new exam to into the database and mark the
						// exam as not uploaded
						$exam = new Application_Model_Exam ();
						$examMapper = new Application_Model_ExamMapper ();
						
						$exam->setSemester(new Application_Model_Semester(array('id'=>$post['semester'])));
						$exam->setType(new Application_Model_ExamType(array('id'=>$post['type'])));
						$exam->setSubType(new Application_Model_ExamSubType(array('id'=>$post['subType'])));
						$exam->setDegree ( new Application_Model_Degree(array('id'=>$post['degree'])) );
						$exam->setWrittenDegree(new Application_Model_ExamDegree(array('id'=>$post['degree_exam'])));
						$exam->setUniversity(new Application_Model_ExamUniversity(array('id'=>$post['university'])));
						$exam->setComment($post['comment']);
						$exam->setAutor($post['autor']);
						$courses = array();
						foreach ($post['course'] as $course) { $courses[] = new Application_Model_Course(array('id'=>$course)); }
						$exam->setCourse($courses);
						$lecturers = array();
						foreach ($post['lecturer'] as $lecturer) { $lecturers[] = new Application_Model_Course(array('id'=>$lecturer)); }
						$exam->setLecturer($lecturers);
						
						// $exam->setOptions ( $post );
						//$exam->setDegree ( null );
						
						//$exam->setDegreeId ( new Application_Model_Degree(array('id'=>$this->getRequest ()->degree)) );
						
						$examId = $examMapper->saveAsNewExam ( $exam );
						$exam->setId ( $examId );
						
						$data = array ();
						$data ['exam'] = $examId;
						$this->_helper->Redirector->setGotoSimple ( 'upload', null, null, $data );
					} else {
						
					}
					break;
				case 3 :
					$examMapper = new Application_Model_ExamMapper ();
					if (! $this->getRequest ()->isPost ()) {
						$exam = $examMapper->findUpload ( $this->getRequest ()->exam );
						if ($exam->id != $this->getRequest ()->exam) {
							throw new Zend_Exception ( "Sorry, no exam found." );
							// the status id is save as key, so if stauts[1] isset exam, hase sthe status 1
						} else if ($exam->status->id != Application_Model_ExamStatus::NothingUploaded) {
							throw new Zend_Exception ( "Sorry, you can't upload twice!" );
						}
					}
					
					$config = Zend_Registry::get ( 'examDBConfig' );
					$dir = $config ['storagepath'];
					$form = new Application_Form_UploadFile ();
					$form->setExamId ( $this->getRequest ()->exam );
					$form->setAction ( '/exams/upload/exam/' . $this->getRequest ()->exam );
					if (isset ( $this->getRequest ()->files )) {
						$form->setAction ( '/exams/upload/exam/' . $this->getRequest ()->exam . '/files/' . $this->getRequest ()->files );
					}
					$form->setMultiFile ( $this->getRequest ()->files );
					
					if ($this->getRequest ()->isPost ()) {
						if ($form->isValid ( $this->getRequest ()->getParams () )) {
							$post = $this->getRequest ()->getParams ();
							
							$exam = $examMapper->findUpload ( $post ['examId'] );
							if ($exam->id != $post ['examId'] || $exam->status != Application_Model_ExamStatus::NothingUploaded) {
								throw new Zend_Exception ( "Sorry, you can't upload twice!" );
							}
							
							if ($form->exam_file->receive ()) {
								$fileManger = new Application_Model_ExamFileManager ();
								// save the received files
								$fileManger->storeUploadedFiles ( $form->exam_file->getFileName (), $post ['examId'] );
								// update exam to unhecked
								$examMapper->updateExamStatusToUnchecked ( $post ['examId'] );
								// redirect to final page
								$this->_helper->Redirector->setGotoSimple ( 'upload_final' );
							} else {
								break;
							}
						}
					}
					break;
				default :
			
			}
		
		} else {
			if ($form == null)
				$form = new Application_Form_UploadDegrees ();
		}
		
		$this->view->form = $form;
	}
	
	// This empty action is required to output a html page thanking user for his upload
	public function uploadfinalAction() {	}	
	
	public function reportAction() {
		$examid = $this->getRequest ()->id;
		$form = new Application_Form_ExamReport ();
		$form->setAction ( $examid );
		if ($this->_request->isPost ()) {
			$formData = $this->_request->getParams ();
			$examMapper = new Application_Model_ExamMapper ();
			// TODO check escaping as a get variable is passed to mysql here!!!! Maybe find a nicer way of doing this when it's less late.		
			$examMapper->updateExamStatusToReported ( $examid, $formData['_reason']);
			echo "Your report was submitted. Thank you for your help.";
		}
		$this->view->form = $form;
	}

    public function quickSearchAction()
    {
    	$found = false;
    	$form = new Application_Form_ExamQuickSearch();
    	if ($this->_request->isPost()) {
    		$formData = $this->_request->getParams();
    		if ($form->isValid($this->getRequest()->getParams())) {
	       		$index = new Application_Model_ExamSearch();
	       		$found = $index->searchExists($formData['_query']);
	    		if(!$found) {
	    			$form->getElement("_query")->addError("no results found!");
	    		}
    		} else { var_dump($form->getErrors()); }
    	}
    	$this->view->form = $form;
    	
    	// draw the form first, so ists possible to use the back key from your browser to modify the search
    	if($found) {
    		$data['request'] = $formData['_query'];
    		return $this->_helper->Redirector->setGotoSimple ( 'search', null, null, $data );
    	}
    	
    }
}
