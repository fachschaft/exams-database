<?php

class ExamsAdminController extends Zend_Controller_Action {
	
	public function init() {
		// check if a login exists for admin controller
		if (! Zend_Auth::getInstance ()->hasIdentity () && $this->getRequest ()->getActionName () != "login") {
			$data = $this->getRequest ()->getParams ();
			// save the old controller and action to redirect the user after the login
			$authmanager = new Application_Model_AuthManager ();
			$data = $authmanager->pushParameters ( $data );
			
			$this->_helper->Redirector->setGotoSimple ( 'login', null, null, $data );
		}
	}
	
	public function indexAction() {
		// action body
	}
	
	public function overviewAction() {
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
		
		$this->view->exams = $examMapper->fetchAdmin ();
	}
	
	public function editdetailsAction() {
		if ($this->getRequest ()->isPost ()) {
			// save changes
			$form = new Application_Form_AdminDetail ();
			$post = $this->getRequest ()->getPost ();
			$form->setAction ( '/exams-admin/editdetails/id/' . $post ['exam_id'] );
			$form->setCourseOptions ( $post ['degree'] );
			$form->setLecturerOptions ( $post ['degree'] );
			$form->setExamId ( $post ['exam_id'] );
			$form->setDegree ( $post ['degree'] );
			if ($form->isValid ( $this->getRequest ()->getPost () )) {
				$exam = new Application_Model_Exam ();
				$exam->setId ( $post ['exam_id'] )->setCourse ( $post ['course'] )->setLecturer ( $post ['lecturer'] )->setSemester ( $post ['semester'] )->setType ( $post ['type'] )->setSubType ( $post ['subType'] )->setComment ( $post ['comment'] )->setUniversity ( $post ['university'] )->setAutor ( $post ['autor'] )->setDegree ( $post ['degree_exam'] );
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
			$exam = $examMapper->findAdmin ( $id );
			
			$form = new Application_Form_AdminDetail ();
			$form->setAction ( '/exams-admin/editdetails/id/' . $id );
			$form->setExamId ( $exam->Id );
			$form->setDegree ( $exam->degreeId );
			$form->setCourseOptions ( $exam->degreeId, $exam->course );
			$form->setLecturerOptions ( $exam->degreeId, $exam->lecturer );
			$form->setSemesterOptions ( $exam->semester );
			$form->setExamTypeOptions ( $exam->type );
			$form->setExamSubType ( $exam->SubType );
			$form->setExamDegreeOptions ( $exam->degree );
			$form->setExamUniversityOptions ( $exam->University );
			$form->setExamComment ( $exam->comment );
			$form->setExamAutor ( $exam->autor );
			
			$this->view->form = $form;
		
		}
	}
	public function logAction() {
		// action body
		if (! isset ( $this->getRequest ()->id )) {
			// TODO Do something here
		} else {
			$logMapper = new Application_Model_LogMapper ();
			$log = $logMapper->fetchByExam ( $this->getRequest ()->id );
			$this->view->log = $log->logMessages;
		
		}
	}
	public function loginAction() {
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
			if (!$authmanager->checkLogin($formdata)) {
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
	
	public function editfilesAction() {
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
	}
	private function getLoginForm() {
		return new Application_Form_AdminLogin ( array (
				'action' => '',
				'method' => 'post' 
		) );
	}
	
	
	public function logoutAction() {
		Zend_Auth::getInstance ()->clearIdentity ();
		$this->_helper->redirector ( 'login' ); // back to login page
	}
	
	public function buildQuicksearchIndexAction() {
		$form = new Application_Form_AdminQuicksearch ();
		$form->newIndex->setLabel ( 'Create new Index' );
		$form->rebuildIndex->setLabel ( 'Rebuild Index from Database' );
		$form->deleteIndex->setLabel ( 'Delete the Index' );
		$form->optimizeIndex->setLabel ( 'Collect garbage' );
		
		$this->view->form = $form;
		if ($this->getRequest ()->isPost ()) {
			$formData = $this->getRequest ()->getPost ();
			
			if ($form->isValid ( $formData ) && $form->newIndex->isChecked ()) {
				$index = new Application_Model_ExamSearch ();
				$index->createIndex ();
				echo "Index created";
			
			} else if ($form->isValid ( $formData ) && $form->rebuildIndex->isChecked ()) {
				$index = new Application_Model_ExamSearch ();
				$index->renewIndex ();
				echo "Index rebuilt";
			
			} else if ($form->isValid ( $formData ) && $form->deleteIndex->isChecked ()) {
				$index = new Application_Model_ExamSearch ();
				$index->deleteIndex ();
				echo "Index deleted";
			
			} else if ($form->isValid ( $formData ) && $form->optimizeIndex->isChecked ()) {
				$index = new Application_Model_ExamSearch ();
				$index->optimizeIndex ();
				echo "Garbage removed, index optimized";
			
			} else
				throw new Exception ( "Invalid Form Data" );
		}
	}

}