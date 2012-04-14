<?php

class ExamsController extends Zend_Controller_Action {
	
	public function init() {
	}
	
	// Scrub entries from $_POST[] that are not needed
	private function scrubPost(array $scrubEntries, array $scrubIfAllSelected = NULL) {
		$_post = $this->getRequest ()->getPost ();
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
			if ($form->isValid ( $this->getRequest ()->getPost () )) {
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
            
            if($form->isValid($this->getRequest()->getPost())) {
            	$post = $this->scrubPost(array(submit, group));
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
        if(isset($this->getRequest()->exam)) {
        	$exams = array();
        	if(!is_array($this->getRequest()->exam)) { $this->getRequest()->setParam('exam', array($this->getRequest()->exam)); }
        	foreach($this->getRequest()->exam as $id)
        	{
        		$examsMapper = new Application_Model_ExamMapper();
        		$exam = $examsMapper->find($id);
        		if($exam->status->id == Application_Model_ExamStatus::PublicExam)
        			$exams[] = $exam;
        	}
        	
        	$this->view->exams = $exams;
        } else {
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
    }
	
	public function downloadAction() {
		$authmanager = new Application_Model_AuthManager ();
		if (isset ( $this->getRequest ()->id )) {
			// For anonymous Users, check if the user is allowed to download
			// files based on IP
			if (! Zend_Auth::getInstance ()->hasIdentity ()) {
				$ip = array ('ip' => $this->getRequest ()->getClientIp ());
				if (!$authmanager->grantPermission($ip))
					throw new Exception ( 'Sorry, your not allowed to download a file', 401 );
			}
			// If user is allowed to download, get the fileid for the download
			$fileId = $this->getRequest ()->id;
		} 

		else if (isset ( $this->getRequest ()->admin )) {
			// ToDo: check for admin state
			
			// check if a login exists for admin controller
			if (Zend_Auth::getInstance ()->hasIdentity ()) {
				// If user is logged in, get the fileid for the download
				$fileId = $this->getRequest ()->admin;
			} else {
				$data = $this->getRequest ()->getParams ();
				// save the old controller and action to redirect the user after the login
				$data = $authmanager->pushParameters ( $data );
				
				$this->_helper->Redirector->setGotoSimple ( 'login', 'exams-admin', null, $data );
			}
		} else
			throw new Exception ( 'Invalid request', 400 );
			
			// Send the User the file he requested for Download.
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
					if ($form->isValid ( $this->getRequest ()->getPost () )) {
						$post = $this->getRequest ()->getPost ();
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
					if ($form->isValid ( $this->getRequest ()->getPost () )) {
						$post = $this->getRequest ()->getPost ();
						
						// insert the new exam to into the database and mark the
						// exam as not uploaded
						$exam = new Application_Model_Exam ();
						$examMapper = new Application_Model_ExamMapper ();
						
						var_dump($post);
						$exam->setSemester(new Application_Model_Semester(array('id'=>$post['semester'])));
						$exam->setType(new Application_Model_ExamType(array('id'=>$post['type'])));
						$exam->setSubType(new Application_Model_ExamSubType(array('id'=>$post['subType'])));
						$exam->setDegree ( new Application_Model_Degree(array('id'=>$this->getRequest ()->degree)) );
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
					}
					break;
				case 3 :
					$examMapper = new Application_Model_ExamMapper ();
					if (! $this->getRequest ()->isPost ()) {
						$exam = $examMapper->find ( $this->getRequest ()->exam );
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
						if ($form->isValid ( $this->getRequest ()->getPost () )) {
							$post = $this->getRequest ()->getPost ();
							
							$exam = $examMapper->find ( $post ['examId'] );
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
       		$index = new Application_Model_ExamSearch();
    		$exam = $index->searchIndex($formData['_query']);
    		$data = array('exam', $exam);
    		return $this->_helper->Redirector->setGotoSimple ( 'search', null, null, $data );
    	}
    	$this->view->form = $form;
    }
}
