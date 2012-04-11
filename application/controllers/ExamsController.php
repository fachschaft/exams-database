<?php

class ExamsController extends Zend_Controller_Action {
	
	public function init() {
	}
	
	public function indexAction() {
		$this->_helper->redirector ( 'groups' );
	}
	
	public function groupsAction() {
		$form = new Application_Form_DegreeGroups ();
		$this->view->form = $form;
		
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $this->getRequest ()->getPost () )) {
				$post = $this->getRequest ()->getPost ();
				if (isset ( $post ['submit'] ))
					unset ( $post ['submit'] );
				return $this->_helper->Redirector->setGotoSimple ( 'degrees', null, null, $post );
			}
		}
	
	}
	
	public function degreesAction() {
	$form = new Application_Form_ExamDegrees();
        
        if ($this->getRequest()->isPost()) {
            $form->setMultiOptions($this->getRequest()->group);
            $form->setGroup($this->getRequest()->group);
            
            if($form->isValid($this->getRequest()->getPost())) {
                $post = $this->getRequest()->getPost();
                if (isset($post['submit']))
                    unset($post['submit']);
                if (isset($post['group']))
                    unset($post['group']);
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
            if($form->isValid($this->getRequest()->getPost())) {
                $post = $this->getRequest()->getPost();
                // remove parameter witch not be needed
                if (isset($post['submit']))
                    unset($post['submit']);
                if (isset($post['lecturer']) && in_array(-1, $post['lecturer']))
                    unset($post['lecturer']);
                if (isset($post['course']) && in_array(-1, $post['course']))
                    unset($post['course']);
                if (isset($post['semester']) && in_array(-1, $post['semester']))
                    unset($post['semester']);
                if (isset($post['examType']) && in_array(-1, $post['examType']))
                    unset($post['examType']);
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
     
        // go back to degree
        if(!isset($this->getRequest()->degree)) {
            return $this->_helper->redirector('groups');
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
        $this->view->exams = $exams->fetch(
                        $this->getRequest()->course,
                        $this->getRequest()->lecturer,
                        $this->getRequest()->semester,
                        $this->getRequest()->examType, 
                        $this->getRequest()->degree,
						array(3,5)	// 3 means public state
                        );
    }
	
	public function downloadAction() {
		// TODO: move this into bootstrap
		date_default_timezone_set ( 'UTC' );
		
		if (isset ( $this->getRequest ()->id )) {
			if (! Zend_Auth::getInstance ()->hasIdentity ()) {
				$adapter = new Custom_Auth_Adapter_InternetProtocol ( $this->getRequest ()->getClientIp () );
				$auth = Zend_Auth::getInstance ();
				$result = $auth->authenticate ( $adapter );
				if (! $result->isValid ()) {
					throw new Exception ( 'Sorry, your not allowed to download a file', 500 );
				}
			}
			$fileId = $this->getRequest ()->id;
			$filemanager = new Application_Model_ExamFileManager ();
			$filemanager->downloadDocuments ( $fileId );
			exit ();
		
		} 
		
		else if (isset ( $this->getRequest ()->admin )) {
			// ToDo: check for admin state
			
			// check if a login exists for admin controller
			if (! Zend_Auth::getInstance ()->hasIdentity ()) {
				$data = $this->getRequest ()->getParams ();
				// save the old controller and action to redirect the user after
				// the login
				if (! isset ( $data ['rcontroller'] ) || isset ( $data ['raction'] )) {
					$data ['rcontroller'] = $data ['controller'];
					$data ['raction'] = $data ['action'];
				}
				unset ( $data ['controller'] );
				unset ( $data ['action'] );
				$this->_helper->Redirector->setGotoSimple ( 'login', 'exams-admin', null, $data );
			} 
			
			else {
				$fileId = $this->getRequest ()->admin;
				$filemanager = new Application_Model_ExamFileManager ();
				$filemanager->downloadDocuments ( $fileId );
				exit ();
			}
		} 
		
		else {
			throw new Exception ( 'Invalid document called', 500 );
		}
	
	}
	
	public function uploadAction() {
		
		$form = null;
		$step = 1;
		
		if (isset ( $this->getRequest ()->degree )) {
			$step = 2;
			$form = new Application_Form_UploadDetail ();
			$form->setCourseOptions ( $this->getRequest ()->degree );
			$form->setLecturerOptions ( $this->getRequest ()->degree );
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
					if ($form->isValid ( $this->getRequest ()->getPost () )) {
						$post = $this->getRequest ()->getPost ();
						unset ( $post ['submit'] );
						unset ( $post ['step'] );
						$this->_helper->Redirector->setGotoSimple ( 'upload', null, null, $post );
					}
					break;
				case 2 :
					$form = new Application_Form_UploadDetail ();
					$form->setCourseOptions ( $this->getRequest ()->degree );
					$form->setLecturerOptions ( $this->getRequest ()->degree );
					$form->setDegree ( $this->getRequest ()->degree );
					if ($form->isValid ( $this->getRequest ()->getPost () )) {
						$post = $this->getRequest ()->getPost ();
						
						// insert the new exam to into the database and mark the
						// exam as not uploaded
						$exam = new Application_Model_Exam ();
						$examMapper = new Application_Model_ExamMapper ();
						
						$exam->setOptions ( $post );
						$exam->setDegree ( null );
						$exam->setDegree ( $post ['degree_exam'] );
						$exam->setDegreeId ( $post ['degree'] );
						
						$examId = $examMapper->saveAsNewExam ( $exam );
						$exam->setId ( $examId );
						
						$data = array ();
						$data ['exam'] = $examId;
						$this->_helper->Redirector->setGotoSimple ( 'upload', null, null, $data );
					}
					break;
				case 3 :
					$examMapper = new Application_Model_ExamMapper ();
					if (! $this->getRequest ()->isPost ()) {
						$exam = $examMapper->find ( $this->getRequest ()->exam );
						if ($exam->id != $this->getRequest ()->exam) {
							throw new Zend_Exception ( "Sorry, no exam found." );
						} else if ($exam->status != 1) {
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
						if ($form->isValid ( $this->getRequest ()->getPost () )) {
							$post = $this->getRequest ()->getPost ();
							
							$exam = $examMapper->find ( $post ['examId'] );
							if ($exam->id != $post ['examId'] || $exam->status != 1) {
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
	
	public function uploadfinalAction() {
		// action body
	}
	
	public function reportAction() {
		$examid = $this->getRequest ()->id;
		$form = new Application_Form_ExamReport ();
		$form->setAction ( $examid );
		if ($this->_request->isPost ()) {
			$formData = $this->_request->getPost ();
			$examMapper = new Application_Model_ExamMapper ();
			// TODO check escaping as a get variable is passed to mysql here!!!! Maybe find a nicer way of doing this when it's less late.		
			// TODO This changes status to 5, meaning the exam disappeares from user search AND admin overwiew :) capt'n, we're losing exams! make the user search show exams status 5 as well as 3.
			$examMapper->updateExamStatusToReported ( $examid );
			echo "Your report was submitted. Thank you for your help.";
		}
		$this->view->form = $form;
	}


    public function quickSearchAction()
    {
    	$form = new Application_Form_ExamQuickSearch();
    	if ($this->_request->isPost()) {
    		$formData = $this->_request->getPost();
    		if (!$form->isValid($formData)) throw new Exception('Invalid Form Data');
    		 
    		echo "You searched for $formData[_query]"; 
    		exit;
    	}
    	$this->view->form = $form;
    }
}







