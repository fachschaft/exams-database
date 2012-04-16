<?php

class ExamsAdminController extends Zend_Controller_Action
{

    public function init()
    {
		// check if a login exists for admin controller
		if (! Zend_Auth::getInstance ()->hasIdentity () && $this->getRequest ()->getActionName () != "login") {
			$data = $this->getRequest ()->getParams ();
			// save the old controller and action to redirect the user after the login
			$authmanager = new Application_Model_AuthManager ();
			$data = $authmanager->pushParameters ( $data );
			
			$this->_helper->Redirector->setGotoSimple ( 'login', null, null, $data );
		
		}
		//
    }

    public function indexAction()
    {
		// action body
    }

    public function overviewAction()
    {
		$examMapper = new Application_Model_ExamMapper ();
		$request = $this->getRequest ();
		if (isset ( $request->do ) && isset ( $request->id )) {
			$do = $request->do;
			$id = $request->id;
			
			switch ($do) {
				case "approve" :
					$examMapper->updateExamStatusToChecked ( $id );
					$this->_helper->Redirector->setGotoSimple ( 'overview' );
					break;
				case "disapprove" :
					$examMapper->updateExamStatusToDisapprove ( $id );
					$this->_helper->Redirector->setGotoSimple ( 'overview' );
					break;
				case "unreport" :
					$examMapper->updateExamStatusUnreport ( $id );
					$this->_helper->Redirector->setGotoSimple ( 'overview' );
				break;
				case "delete" :
					$examMapper->updateExamStatusToDelete ( $id );
					$this->_helper->Redirector->setGotoSimple ( 'overview' );
					break;
				case "edit" :
					$data = array ();
					$data ['id'] = $request->id;
					$this->_helper->Redirector->setGotoSimple ( 'editdetails', null, null, $data );
					break;
				case "edit-files" :
					$data = array ();
					$data ['id'] = $request->id;
					$this->_helper->Redirector->setGotoSimple ( 'editfiles', null, null, $data );
					break;
				case "log" :
					$data = array ();
					$data ['id'] = $request->id;
					$this->_helper->Redirector->setGotoSimple ( 'log', null, null, $data );
					break;
				default :
					throw new Zend_Exception ( "Sorry, the do action (\"" . $do . "\") is not implemented." );
					break;
			}
		}
		
		$this->view->exams = $examMapper->fetchUnchecked ();
		
		$this->view->exams_reported = $examMapper->fetchReported ();
    }

    public function editdetailsAction()
    {
		if ($this->getRequest ()->isPost ()) {
			// save changes
			$form = new Application_Form_AdminDetail ();
			$post = $this->getRequest ()->getPost ();
			$form->setAction ( '/exams-admin/editdetails/id/' . $post ['exam_id'] );
			$form->setCourseOptions ( new Application_Model_Degree(array('id'=>$post ['degree'])) );
			$form->setLecturerOptions ( new Application_Model_Degree(array('id'=>$post ['degree'])) );
			$form->setExamId ( $post ['exam_id'] );
			$form->setDegree ( $post ['degree'] );
			if ($form->isValid ( $this->getRequest ()->getPost () )) {
				$exam = new Application_Model_Exam ();
				var_dump($exam);
				$exam->setId ( $post ['exam_id'] );
				$corses = array();
				foreach ($post ['course'] as $cor) { $corses[] = new Application_Model_Course(array('id'=>$cor));  }
				$exam->setCourse ( $corses );
				$lecturer = array();
				foreach ($post ['lecturer'] as $lec) {
					$lecturer[] = new Application_Model_Lecturer(array('id'=>$lec));
				}
				$exam->setCourse ( $corses );
				$exam->setLecturer ( $lecturer );
				$exam->setSemester ( new Application_Model_Semester(array('id'=>$post ['semester'])) );
				$exam->setType ( new Application_Model_ExamType(array('id'=>$post ['type'])) );
				$exam->setSubType ( new Application_Model_ExamSubType(array('id'=>$post ['subType'])) );
				$exam->setComment ( $post ['comment'] );
				$exam->setUniversity ( new Application_Model_ExamUniversity(array('id'=>$post ['university'])) );
				$exam->setAutor ( $post ['autor'] );
				$exam->setDegree ( new Application_Model_Degree(array('id'=>$post ['degree_exam'])) );
				$examMapper = new Application_Model_ExamMapper ();
				$examMapper->updateExam ( $exam );
				$this->_helper->Redirector->setGotoSimple ( 'overview' );
			} else {
				$this->view->form = $form;
			}
		} else {
			
			if (! isset ( $this->getRequest ()->id )) {
				throw new Zend_Exception ( "No id given." );
			}
			$id = $this->getRequest ()->id;
			
			$examMapper = new Application_Model_ExamMapper ();
			$exam = $examMapper->find( $id );
			
			$form = new Application_Form_AdminDetail ();
			$form->setAction ( '/exams-admin/editdetails/id/' . $id );
			$form->setExamId ( $exam->id );
			$form->setDegree ( $exam->degree->id );
			$form->setCourseOptions ( $exam->degree, $exam->course );
			$form->setLecturerOptions ( $exam->degree, $exam->lecturer );
			$form->setSemesterOptions ( $exam->semester );
			$form->setExamTypeOptions ( $exam->type );
			$form->setExamSubType ( $exam->SubType );
			$form->setExamDegreeOptions ( $exam->degree );
			$form->setExamUniversityOptions ( $exam->University );
			$form->setExamComment ( $exam->comment );
			$form->setExamAutor ( $exam->autor );
			
			$this->view->form = $form;
		
			//
		}
		
    }

    public function logAction()
    {
		// action body
		if (! isset ( $this->getRequest ()->id )) {
			// TODO Do something here
		} else {
			$logMapper = new Application_Model_LogMapper ();
			$log = $logMapper->fetchByExam ( $this->getRequest ()->id );
			$this->view->log = $log->logMessages;
		
		}
		//
    }

    public function loginAction()
    {
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

    public function editfilesAction()
    {
		// catch post actions
		if ($this->getRequest ()->isPost ()) {
			$post = $this->getRequest ()->getPost ();
			if (isset ( $post ['action'] ) && isset ( $post ['id'] )) {
				$ids = $post ['id'];
				
				$fileManger = new Application_Model_ExamFileManager ();
				$documentMapper = new Application_Model_DocumentMapper ();
				$documents = $documentMapper->fetchByExamId ( $this->getRequest ()->id );
				
				switch ($post ['action']) {
					case 'delete' :
						foreach ( $ids as $id ) {
							$documentMapper->deleteDocument ( $id );
						}
						break;
					case 'pack' :
						$docs = array ();
						foreach ( $documents as $doc ) {
							if (in_array ( $doc->id, $ids )) {
								// check if the document is in the selected one
								$docs [] = $doc;
							}
						}
						$fileManger->packDocuments($docs);
					break;
					case 'unpack':
						foreach($documents as $doc) {
							// check if the document is in the selected one
							if(in_array($doc->id, $ids)) {
								$fileManger->unpackDocuments(array($doc)); // short workaround
							}
						}
						break;
					default :
						throw new Exception ( 'Action not implemented', 500 );
						break;
				}
			
			} else {
				$form2 = new Application_Form_AdminFileUpload ();
				if ($form2->isValid ( $this->getRequest ()->getPost () )) {
					if ($form2->exam_file->receive ()) {
						$fileManger = new Application_Model_ExamFileManager ();
						// save the received files
						$fileManger->storeUploadedFiles ( $form2->exam_file->getFileName (), $this->getRequest ()->id );
						$this->_helper->Redirector->setGotoSimple ( 'editfiles', null, null, array (
								'id' => $this->getRequest ()->id 
						) );
					}
				}
			}
		}
		
		// catch do actions
		if (isset ( $this->getRequest ()->do )) {
			if (! isset ( $this->getRequest ()->file )) {
				throw new Exception ( 'No file ID given', 500 );
			}
			$documentMapper = new Application_Model_DocumentMapper ();
			
			// switch over the given action
			switch ($this->getRequest ()->do) {
				case "delete" :
					$documentMapper->deleteDocument ( $this->getRequest ()->file );
					break;
				default :
					throw new Exception ( 'Action not implemented', 500 );
					break;
			}
		}
		
		// catch regular displaying
		if (! isset ( $this->getRequest ()->id )) {
			$this->_helper->Redirector->setGotoSimple ( 'overview', null );
		} else {
			$documentMapper = new Application_Model_DocumentMapper ();
			$documents = $documentMapper->fetchByExamId ( $this->getRequest ()->id );
			
			$form = new Application_Form_AdminFileDetail ();
			$form->setId ( $this->getRequest ()->id );
			$form->setupDocuments ( $documents );
			$this->view->form = $form;
			
			$form2 = new Application_Form_AdminFileUpload ();
			$this->view->files = 3;
			$this->view->exam = $this->getRequest ()->id;
			if (isset ( $this->getRequest ()->files )) {
				$form2->setMultiFile ( $this->getRequest ()->files );
				$this->view->files = $this->getRequest ()->files + 2;
			}
			
			$this->view->form2 = $form2;
		
		}
		//
    }

    private function getLoginForm()
    {
		return new Application_Form_AdminLogin ( array (
				'action' => '',
				'method' => 'post' 
		) );
    }

    public function logoutAction()
    {
		Zend_Auth::getInstance ()->clearIdentity ();
		$this->_helper->redirector ( 'login' ); // back to login page
    }

    public function buildQuicksearchIndexAction()
    {
		$form = new Application_Form_AdminQuicksearch ();
		$form->newIndex->setLabel ( 'Create new Index' );
		$form->rebuildIndex->setLabel ( 'Rebuild Index from Database' );
		$form->deleteIndex->setLabel ( 'Delete the Index' );
		$form->optimizeIndex->setLabel ( 'Collect garbage' );
		$form->indexSize->setLabel ('Return index filecount ');
		
		$this->view->form = $form;
		if ($this->getRequest ()->isPost ()) {
			$formData = $this->getRequest ()->getPost ();
			if ($form->isValid ( $formData )) {
				$index = new Application_Model_ExamSearch ();
				
				if ($form->newIndex->isChecked ()) {
					$index->createIndex ();
					echo "Index created";
				
				} else if ($form->rebuildIndex->isChecked ()) {
					$index->renewIndex ();
					echo "Index rebuilt";
				
				} else if ($form->deleteIndex->isChecked ()) {
					$index->deleteIndex ();
					echo "Index deleted";
				
				} else if ($form->optimizeIndex->isChecked ()) {
					$index->optimizeIndex ();
					echo "Garbage removed, index optimized";
				
				} else if ($form->indexSize->isChecked()){
					$size = $index->getIndexSize();
					echo "There are $size files in the Index";
				}
			} else
				throw new Exception ( "Invalid Form Data" );
			
		}
		//
    }

    public function degreeGroupAction()
    {
        $form = new Application_Form_AdminDegreeGroup();
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();
        	$action = null;
        	
        	// remove errors if we look for the select action
        	if(isset($this->getRequest()->select)) {
        		$action = 'delete';
        		$form->getElement('newElement')->setRequired(false);
        		$form->getElement('newElement')->clearValidators();
        	}
        	
        	// remove form errors if we look for the add action
        	if(isset($this->getRequest()->add)) {
        		$action = 'add';
        		$form->getElement('group')->setRequired(false);
        		$form->getElement('group')->clearErrorMessages();
        	}

        	if ($form->isValid ( $this->getRequest ()->getPost ()) ) {
        		switch ($action){
        			case 'delete':
        				$groupMapper = new Application_Model_DegreeGroupMapper();
        				foreach($this->getRequest()->group as $groupId)
        				{
        					$groupMapper->delte($groupId);
        				}
        				$form = new Application_Form_AdminDegreeGroup();        				
        				break;
        			case 'add':
        				$groupMapper = new Application_Model_DegreeGroupMapper();
        				$groupMapper->addNewGroup($this->getRequest()->newElement);
        				$form->getElement('newElement')->setValue("");
        				$form = new Application_Form_AdminDegreeGroup();
        				break;
        		}
        	} else {
        		
        	}
        }
    	
        $this->view->form1 =  $form;
    }

    public function degreeAction()
    {
        $form_add = new Application_Form_AdminDegree();
        $form_edit = new Application_Form_AdminDegreeEdit();
        
        
        
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();   	 
        	$action = null;

        	if(isset($this->getRequest()->select_button)) {
        		$action = 'select';
        	}
        	
        	if(isset($this->getRequest()->select_save)) {
        		$action = 'select_save';
        	}
        	
        	if(isset($this->getRequest()->select_delete)) {
        		$action = 'select_delete';
        	}
        	 
        	if(isset($this->getRequest()->add)) {
        		$action = 'add';
        	}
        	
        	
        	
        	
        	switch($action){
        		case 'add':
        			if ($form_add->isValid ( $this->getRequest ()->getPost ()) ) {
        				$new_degree = new Application_Model_Degree(array('name'=>$data['newElement'], 'group'=>new Application_Model_DegreeGroup(array('id'=>$data['group']))));
        				$degreeMapper = new Application_Model_DegreeMapper();
        				$degreeMapper->add($new_degree);
        				// leave the form after save
        				$this->_helper->redirector('degree');
        			}
        			
        			break;
        		case 'select':
        			// disable add form
        			$form_add = null;
        			$degreeId = $data['select_degree'];
        			$degreeMapper = new Application_Model_DegreeMapper();
        			$degree = $degreeMapper->find($degreeId);
        			$form_edit->showEdit($degree->group->id);
        			$form_edit->setDegree($degree->id);
        			break;
        		case 'select_save':
        			// disable add form
        			$form_add = null;
        			$data['select_degree'] = $data['select_degree_2'];
        			$form_edit->setDegree($this->getRequest()->select_degree);
        			$degreeMapper = new Application_Model_DegreeMapper();
        			$degree = $degreeMapper->find($data['select_degree']);
        			$form_edit->showEdit($degree->group->id);
        			
        			if ($form_edit->isValid ( $data ) ) {
        				$degree->group = new Application_Model_DegreeGroup(array('id'=>$data['select_group']));
        				$degreeMapper->updateGroup($degree);
        				// leave the form after save
        				$this->_helper->redirector('degree');
        			}
        			break;
        		case 'select_delete':
        			if ($form_edit->isValid ( $data ) ) {
        				$degreeMapper = new Application_Model_DegreeMapper();
        				$degree = $degreeMapper->find($data['select_degree']);
        				$degreeMapper->delete($degree);
        				// leave the form after save
        				$this->_helper->redirector('degree');
        			}
        			break;
        	}
        

        }
        if($form_add != null) $this->view->form_add = $form_add;
        if($form_edit != null) $this->view->form_edit = $form_edit;
    }

    public function courseAddAction()
    {
        $form = new Application_Form_AdminCourseAdd();
        
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();
        	$action = null;
        
        	var_dump($data);
        	
        	if(isset($this->getRequest()->add)) {
        		$action = 'add';
        	}

        	switch($action){
        		case 'add':
        			if ($form->isValid ( $data ) ) {
        				$new_degrees = array();
        				foreach ($data['degrees'] as $deg) { $new_degrees[] = new Application_Model_Degree(array('id'=>$deg)); }
        				$new_course = new Application_Model_Course(array('name'=>$data['newElement'], 'degrees'=>$new_degrees));
        				$courseMapper = new Application_Model_CourseMapper();
        				$courseMapper->add($new_course);
        				
        				// leave the form after save
        				$this->_helper->redirector('course-add');
        			}
        			break;
        	}
        }
        
        
        $this->view->form = $form;
    }

    public function courseEditAction()
    {
       $form = new Application_Form_AdminCourseEdit();
        
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();
        	$action = null;
        
        	var_dump($data);
        	
        	if(isset($this->getRequest()->select_button)) {
        		$action = 'select';
        	}
        	
        	if(isset($this->getRequest()->select_delete)) {
        		$action = 'delete';
        	}
        	if(isset($this->getRequest()->select_save)) {
        		$action = 'save';
        	}

        	switch($action){
        		case 'delete':
        			break;
        		case 'select':
        			if ($form->isValid ( $data ) ) {
	        			$form->setCourseId($data['select_course']);
	        			$courseMapper = new Application_Model_CourseMapper();
	        			$course = $courseMapper->find($data['select_course']);
	        			$ids = array();
	        			foreach($course->degrees as $deg) { $ids[] = $deg->id; }
	        			$form->showEdit($ids);
        			}
        			break;
        		case 'save':
        			$data['select_course'] = $data['select_course_2'];
        			$form->setCourseId($data['select_course']);
        			if(!isset($data['select_degrees'])) {
        				$data['select_degrees'] = array();
        			}
        			$form->showEdit($data['select_degrees']);
        			if ($form->isValid ( $data ) ) {
        				$degrees = array();
        				foreach($data['select_degrees'] as $deg) { $degrees[] = new Application_Model_Degree(array('id'=>$deg));  }
        				$course = new Application_Model_Course(array('id'=>$data['select_course'], 'degrees'=>$degrees));
        				$courseMapper = new Application_Model_CourseMapper();
        				$courseMapper->update($course);
        				
        				// leave the form after save
        				$this->_helper->redirector('course-edit');
        			}     			
        			$form->getElement('select_degrees')->addError("select at least one degree!");
        			break;
        	}
        }
        
        
        $this->view->form = $form;
    }


}







