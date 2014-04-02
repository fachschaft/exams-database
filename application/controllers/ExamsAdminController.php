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

class ExamsAdminController extends Zend_Controller_Action
{
	private $_authManager;

    public function init()
    {	
    	$this->_authManager = new Application_Model_AuthManager();
		// check if a login exists for admin controller
		if ((!$this->_authManager->isAllowed(null, 'view_admin_interface'))) {
			$data = $this->getRequest ()->getParams ();
			// save the old controller and action to redirect the user after the login
			$authmanager = new Application_Model_AuthManager ();
			$data = $authmanager->pushParameters ( $data );
			
			$this->_helper->Redirector->setGotoSimple ( 'index', 'login', null, $data );
		
		}
		
		$this->view->jQuery()->enable();
		$this->view->jQuery()->uiEnable();
		
		//
    }

    public function indexAction()
    {
		// action body
    }

    public function overviewAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'view_admin_interface')) {
    		$this->_helper->Redirector->setGotoSimple ('index');
    	}
    	
		$examMapper = new Application_Model_ExamMapper ();
		$documentMapper = new Application_Model_DocumentMapper();
		$request = $this->getRequest ();
		if (isset ( $request->do ) && isset ( $request->id )) {
			$do = $request->do;
			$id = $request->id;
			
			switch ($do) {
				case "approve" :
					if(!$this->_authManager->isAllowed(null, 'approve_exam')) {
						echo "You can't do that!";
								break;
					}
					
					$docs = $documentMapper->fetchByExamId($id);
					$allProofed = true;
					if(!empty($docs)) {
						foreach ($docs as $doc) {
							if(!$doc->getReviewed()) {
								$allProofed = false;
							}
						}
					} else {
						$allProofed = false;
					}
					
					if ($allProofed) {
						$examMapper->updateExamStatusToChecked ( $id );
					}
					$this->_helper->Redirector->setGotoSimple ( 'overview' );
					
					break;
				case "disapprove" :
					if(!$this->_authManager->isAllowed(null, 'approve_exam')) {
						echo "You can't do that!";
						break;
					}
					$examMapper->updateExamStatusToDisapprove ( $id );
					$this->_helper->Redirector->setGotoSimple ( 'overview' );
					break;
				case "unreport" :
					if(!$this->_authManager->isAllowed(null, 'approve_exam')) {
						echo "You can't do that!";
						break;
					}
					$examMapper->updateExamStatusUnreport ( $id );
					$this->_helper->Redirector->setGotoSimple ( 'overview' );
				break;
				case "delete" :
					if(!$this->_authManager->isAllowed(null, 'modify_exam')) {
						echo "You can't do that!";
						break;
					}
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
    
    
    public function ensureAction()
    {
    	$request = $this->getRequest ();
    	if (isset ( $request->do ) && isset ( $request->id )) {
    		$do = $request->do;
    		$id = $request->id;    		
    		
    			
    		switch ($do) {
    			case "delete_exam" :
    				$this->view->action = 'exam_delete';
    				$this->view->id = $id;
    				break;
    				
    				
    			case "delete_file" :
    				$this->view->action = 'delete_file';
    				$this->view->id = $id;
    				$this->view->edit_exam = $request->edit_exam;
    				break;
    			case "delete_file_do" :
    				$documentMapper = new Application_Model_DocumentMapper ();
    				$documentMapper->deleteDocument ( $id );
    				$data2['id'] = $this->getRequest()->edit_exam;
    				$this->_helper->Redirector->setGotoSimple ( 'editfiles', null, null, $data2 );
    				break;
    				
    				
    			case "delete_files" :
    				$this->view->action = 'delete_files';
    				if(!is_array($id)) {
    					$this->view->id = array($id);
    				} else {
    					$this->view->id = $id;
    				}
    				$this->view->edit_exam = $request->edit_exam;
    				break;
    			case "delete_files_do" :
    				if(!is_array($id)) {
    					$id = array($id);
    				}
    				foreach ( $id as $i ) {
    					$documentMapper = new Application_Model_DocumentMapper();
    					$documentMapper->deleteDocument ( $i );
    				}
    				$data2['id'] = $this->getRequest()->edit_exam;
    				$this->_helper->Redirector->setGotoSimple ( 'editfiles', null, null, $data2 );
    				break;
    				
    				
    				
    			
    				// degree group handle
    			case "delete_degree_group" :
    				$this->view->action = 'delete_degree_group';
    				$this->view->id = $request->id;
    				break;
    			case "delete_degree_group_do" :
    				$groupMapper = new Application_Model_DegreeGroupMapper();
    				$groupMapper->delete(new Application_Model_DegreeGroup(array('id'=>$id)));
    				// leave the form after save
    				$this->_helper->redirector('degree-group-edit');
    				break;
    				
    				// degree handle
    			case "delete_degree" :
    				$this->view->action = 'delete_degree';
    				$this->view->id = $request->id;
    				break;
    			case "delete_degree_do" :
    				$degreeMapper = new Application_Model_DegreeMapper();
       				$degree = $degreeMapper->find($id);
       				$degreeMapper->delete($degree);
       				// leave the form after save
       				$this->_helper->redirector('degree-edit');
   					break;
   					
   					
   					// course handle
				case "delete_course" :
   					$this->view->action = 'delete_course';
   					$this->view->id = $request->id;
   					break;
   				case "delete_course_do" :
   					$courseMapper = new Application_Model_CourseMapper();
	        		$courseMapper->delete(new Application_Model_Course(array('id'=>$id)));   
	        		// leave the form after save
	        		$this->_helper->redirector('course-edit');
   					break;

   					
   					
   					// course lecturer
   				case "delete_lecturer" :
   					$this->view->action = 'delete_lecturer';
   					$this->view->id = $request->id;
   					break;
   				case "delete_lecturer_do" :
   					$courseMapper = new Application_Model_LecturerMapper();
		        	$courseMapper->delete(new Application_Model_Lecturer(array('id'=>$id)));
		        	// leave the form after save
		        	$this->_helper->redirector('lecturer-edit');
   					break;
   					
    				
    				
    					
    					
    		}
    		
    	}	
	    //
    }

    public function editdetailsAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_exam')) 
    		$this->_helper->Redirector->setGotoSimple ( 'overview' );
    		

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
				$exam->setId ( $post ['exam_id'] );
				$courses = array();
				foreach ($post ['course'] as $cor) { $courses[] = new Application_Model_Course(array('id'=>$cor));  }
				$exam->setCourse ( $courses );
				$lecturer = array();
				foreach ($post ['lecturer'] as $lec) {
					$lecturer[] = new Application_Model_Lecturer(array('id'=>$lec));
				}
				$exam->setCourse ( $courses );
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
    	if(!$this->_authManager->isAllowed(null, 'view_log'))
    		$this->_helper->Redirector->setGotoSimple ( 'overview' );
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
    
    
    public function activityLogAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'view_log'))
    		$this->_helper->Redirector->setGotoSimple ( 'overview' );
    	// action body
    	
    }
    
    public function ajaxActivityLogAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'view_log'))
    		$this->_helper->Redirector->setGotoSimple ( 'overview' );
    	// action body
    	
    	$request = $this->getRequest();
    	    	 
    	$page = 1;
    	$max_elements = 30;
    	if (isset ( $request->elements )) {
    		$max_elements=$request->elements;
    	}
    	 
    	if (isset ( $request->page )) {
    		$page = $request->page;
    	}
    	
    	$aml = new Application_Model_LogMapper();
       
    	$result = $aml->fetchAll($page, $max_elements);
    	
    	$this->_helper->json($result);
    	exit();
    }
  
    

    public function editfilesAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_exam'))
    		$this->_helper->Redirector->setGotoSimple ( 'overview' );
		// catch post actions
		if ($this->getRequest ()->isPost ()) {
			$post = $this->getRequest ()->getPost ();
			if (isset ( $post ['action'] ) && isset ( $post ['id'] )) {
				$ids = $post ['id'];
				
				$fileManger = new Application_Model_ExamFileManager ();
				$documentMapper = new Application_Model_DocumentMapper ();
				$documents = $documentMapper->fetchByExamId ( $this->getRequest ()->id );

				switch ($post ['action']) {
					case 'save' :
						foreach ( $post['id'] as $id ) {
							$documentMapper->updateDisplayName(new Application_Model_Document(array('id'=>$id, 'displayName'=>$post['display_'.$id])));
						}
						break;
					case 'delete' :
						$data2['do'] = 'delete_files';
						$data2['id'] = $ids;
						$data2['edit_exam'] = $this->getRequest()->id;
						$this->_helper->Redirector->setGotoSimple ( 'ensure', null, null, $data2 );
						
						//foreach ( $ids as $id ) {
						//	$documentMapper->deleteDocument ( $id );
						//}
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
					$data2['do'] = 'delete_file';
					$data2['id'] = $this->getRequest()->file;
					$data2['edit_exam'] = $this->getRequest()->id;
					$this->_helper->Redirector->setGotoSimple ( 'ensure', null, null, $data2 );
					
					//$documentMapper = new Application_Model_DocumentMapper ();
					//$documentMapper->deleteDocument ( $this->getRequest ()->file );
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

    public function quicksearchIndexAction()
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
					if($this->_authManager->isAllowed(null, 'maintenance_quicksearch_new_index')){
						$index->createIndex ();
						echo "Index created";
					}
					else echo "You can't do that!";
				
				} else if ($form->rebuildIndex->isChecked ()) {
					if($this->_authManager->isAllowed(null, 'maintenance_quickseach_rebuild_index')){
						$index->renewIndex ();
						echo "Index rebuilt";
					}
					else echo "You can't do that!";
				
				} else if ($form->deleteIndex->isChecked ()) {
					if($this->_authManager->isAllowed(null, 'maintenance_quicksearch_delete_index')){
					$index->deleteIndex ();
					echo "Index deleted";
					}
					else echo "You can't do that!";
				
				} else if ($form->optimizeIndex->isChecked ()) {
					if($this->_authManager->isAllowed(null, 'maintenance_quicksearch_exec_garbage')){
					$index->optimizeIndex ();
					echo "Garbage removed, index optimized";
					}
					else echo "you can't do that";
				
				} else if ($form->indexSize->isChecked()){
					if($this->_authManager->isAllowed(null, 'maintenance_quicksearch_file_count')){
					$size = $index->getIndexSize();
					echo "There are $size files in the Index";
					}
					else echo "You can't do that!";
				}
			} else
				throw new Exception ( "Invalid Form Data" );
			
		}
		//
    }

    public function degreeAddAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'add_degree'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	
    	$form = new Application_Form_AdminDegreeAdd();

    	if($this->getRequest ()->isPost ()) {
    		$data = $this->getRequest ()->getPost ();
    		$action = null;
    
    		if(isset($this->getRequest()->add)) {
    			$action = 'add';
    		}

    		switch($action){
    			case 'add':
    				if ($form->isValid ( $this->getRequest ()->getPost ()) ) {
    					$new_degree = new Application_Model_Degree(array('name'=>$data['newElement'], 'group'=>new Application_Model_DegreeGroup(array('id'=>$data['group']))));
    					$degreeMapper = new Application_Model_DegreeMapper();
    					$degreeMapper->add($new_degree);
    					// leave the form after save
    					$this->_helper->redirector('degree-add');
    				}
    				break;
    		}

    	}
    	if($form != null) $this->view->form = $form;
    }

    public function degreeEditAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_degree'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
        $form = new Application_Form_AdminDegreeEdit();

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

        	switch($action){
        		case 'select':
        			// disable add form
        			$form_add = null;
        			$degreeId = $data['select_degree'];
        			$degreeMapper = new Application_Model_DegreeMapper();
        			$degree = $degreeMapper->find($degreeId);
        			$form->showEdit($degree->name, $degree->group->id);
        			$form->setDegree($degree->id);
        			break;
        		case 'select_save':
        			// disable add form
        			$form_add = null;
        			$data['select_degree'] = $data['select_degree_2'];
        			$form->setDegree($this->getRequest()->select_degree);
        			$degreeMapper = new Application_Model_DegreeMapper();
        			$degree = $degreeMapper->find($data['select_degree']);
        			$form->showEdit($degree->group->id);
        			
        			if ($form->isValid ( $data ) ) {
        				$degree->group = new Application_Model_DegreeGroup(array('id'=>$data['select_group']));
        				$degree->name = $data['new_degree_name'];
        				$degreeMapper->updateGroup($degree);
        				// leave the form after save
        				$this->_helper->redirector('degree-edit');
        			}
        			break;
        		case 'select_delete':
        			
        			if ($form->isValid ( $data ) ) {
        				$data2['do'] = 'delete_degree';
        				$data2['id'] = $data['select_degree'];
        				$this->_helper->Redirector->setGotoSimple ( 'ensure', null, null, $data2 );
        				
        				//$degreeMapper = new Application_Model_DegreeMapper();
        				//$degree = $degreeMapper->find($data['select_degree']);
        				//$degreeMapper->delete($degree);
        				// leave the form after save
        				//$this->_helper->redirector('degree-edit');
        			}
        			break;
        	}
        

        }
        if($form != null) $this->view->form = $form;
    }

    public function courseAddAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'add_course'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	
        $form = new Application_Form_AdminCourseAdd();
        
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();
        	$action = null;
        	
        	if(isset($this->getRequest()->add)) {
        		$action = 'add';
        	}

        	switch($action){
        		case 'add':
        			if ($form->isValid ( $data ) ) {
        				$new_degrees = array();
        				foreach ($data['degrees'] as $deg) { $new_degrees[] = new Application_Model_Degree(array('id'=>$deg)); }
        				$new_course = new Application_Model_Course(array('name'=>$data['newElement'], 'degrees'=>$new_degrees));
        				//var_dump($data);
        				//die();
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
    	if(!$this->_authManager->isAllowed(null, 'modify_course'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	
       $form = new Application_Form_AdminCourseEdit();
        
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();
        	$action = null;

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
        			if ($form->isValid ( $data ) ) {
        				$data2['do'] = 'delete_course';
        				$data2['id'] = $data['select_course'];
        				$this->_helper->Redirector->setGotoSimple ( 'ensure', null, null, $data2 );
        				
	        			//$courseMapper = new Application_Model_CourseMapper();
	        			//$courseMapper->delete(new Application_Model_Course(array('id'=>$data['select_course'])));   
	        			// leave the form after save
	        			//$this->_helper->redirector('course-edit');
        			}
        			break;
        		case 'select':
        			if ($form->isValid ( $data ) ) {
	        			$form->setCourseId($data['select_course']);
	        			$courseMapper = new Application_Model_CourseMapper();
	        			$course = $courseMapper->find($data['select_course']);
	        			$ids = array();
	        			foreach($course->degrees as $deg) { $ids[] = $deg->id; }
	        			$idsConn = array();
	        			foreach($course->connectedCourse as $cor) { $idsConn[] = $cor->id; }
	        			$form->showEdit($course->name, $ids, $idsConn);
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
        				if(!isset($data['select_connected_course'])) { $data['select_connected_course'] = array(); }
        				$connected = array();
        				foreach($data['select_connected_course'] as $cor) { $connected[] = new Application_Model_Course(array('id'=>$cor)); }
        				$course = new Application_Model_Course(array('id'=>$data['select_course'], 'name'=>$data['new_course_name'], 'degrees'=>$degrees, 'connectedCourse'=>$connected));
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

    public function lecturerAddAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'add_lecturer'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	
        $form = new Application_Form_AdminLecturerAdd();
         
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();
        	$action = null;
        	
        	if(isset($this->getRequest()->add)) {
        		$action = 'add';
        	}

        	switch($action){
        		case 'add':
        			if ($form->isValid ( $data ) ) {
        				$new_degrees = array();
        				foreach ($data['degrees'] as $deg) {
        					$new_degrees[] = new Application_Model_Degree(array('id'=>$deg));
        				}
        				$new_lecturer = new Application_Model_Lecturer(array('name'=>$data['newElement'], 'firstName'=>$data['newElementFirstName'], 'degree'=>$data['newElementDegree'], 'degrees'=>$new_degrees));
        				$lecturerMapper = new Application_Model_LecturerMapper();
        				$lecturerMapper->add($new_lecturer);
        			
        				// leave the form after save
        				$this->_helper->redirector('lecturer-add');
        			}
        			break;
        	}
        }
        //
        $this->view->form = $form;
    }

    public function lecturerEditAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_lecturer'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	
    $form = new Application_Form_AdminLecturerEdit();
        
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();
        	$action = null;

        	if(isset($this->getRequest()->select_button)) {
        		$action = 'select';
        	}
        	if(isset($this->getRequest()->select_save)) {
        		$action = 'save';
        	}
        	if(isset($this->getRequest()->select_delete)) {
        		$action = 'delete';
        	}


	        	switch($action){
	        		case 'delete':
	        			if ($form->isValid ( $data ) ) {
	        				$data2['do'] = 'delete_lecturer';
	        				$data2['id'] = $data['select_lecturer'];
	        				$this->_helper->Redirector->setGotoSimple ( 'ensure', null, null, $data2 );
	        				
		        			//$courseMapper = new Application_Model_LecturerMapper();
		        			//$courseMapper->delete(new Application_Model_Lecturer(array('id'=>$data['select_lecturer'])));
		        			// leave the form after save
		        			//$this->_helper->redirector('lecturer-edit');
	        			}
	        			break;
	        		case 'select':
	        			if ($form->isValid ( $data ) ) {
	        				$form->setLecturerId($data['select_lecturer']);
	        				$lecturerMapper = new Application_Model_LecturerMapper();
	        				$lecturer = $lecturerMapper->find($data['select_lecturer']);
	        				$idsDegr = array();
	        				foreach($lecturer->degrees as $degr) {
	        				$idsDegr[] = $degr->id;
	        				}
	        				$form->showEdit($lecturer->name, $lecturer->degree, $lecturer->firstName, $idsDegr);
	        			}
	        			break;
	        		case 'save':
	        			$data['select_lecturer'] = $data['select_lecturer_2'];
	        			$form->setLecturerId($data['select_lecturer']);
	        			if(!isset($data['select_degrees'])) {
	        				$data['select_degrees'] = array();
	        			}
	        			$form->showEdit($data['new_lecturer_name'], $data['newElementDegree'], $data['newElementFirstName'], $data['select_degrees']);
	        			if ($form->isValid ( $data ) ) {
	        				$degrees = array();
	        				foreach($data['select_degrees'] as $deg) {
	        					$degrees[] = new Application_Model_Degree(array('id'=>$deg));
	        				}

	        				$lec = new Application_Model_Lecturer(array('id'=>$data['select_lecturer'], 'name'=>$data['new_lecturer_name'],'degree'=>$data['newElementDegree'],'firstName'=>$data['newElementFirstName'], 'degrees'=>$degrees));
	        				$lecMapper = new Application_Model_LecturerMapper();
	        				$lecMapper->update($lec);
	        			
	        				// leave the form after save
	        				$this->_helper->redirector('lecturer-edit');
	        			}
	        			break;
        		}
        }
        //
        $this->view->form = $form;
    }

    public function degreeGroupAddAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'add_degree_groups'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	
        $form = new Application_Form_AdminDegreeGroupAdd();
        if($this->getRequest ()->isPost ()) {
        	$data = $this->getRequest ()->getPost ();
        	$action = null;

        	if(isset($this->getRequest()->add)) {
        		$action = 'add';
        	}
        	
        	switch ($action){
        		case 'add':
        			if ($form->isValid ( $this->getRequest ()->getPost ()) ) {
        				$groupMapper = new Application_Model_DegreeGroupMapper();
        				$groupMapper->addNewGroup($this->getRequest()->newElement);
        				

        				// leave the form after save
        				$this->_helper->redirector('degree-group-add');
        			}
        			break;
        	}
        }
    	
        $this->view->form =  $form;
    }

    public function degreeGroupEditAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_degree_groups'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	
    	$form = new Application_Form_AdminDegreeGroupEdit();
    	
    	if($this->getRequest ()->isPost ()) {
    		$data = $this->getRequest ()->getPost ();
    		$action = null;
    	
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
    				if ($form->isValid ( $data ) ) {
    					$data2['do'] = 'delete_degree_group';
    					$data2['id'] = $data['select_degree_group'];
    					$this->_helper->Redirector->setGotoSimple ( 'ensure', null, null, $data2 );
    					
    					//$groupMapper = new Application_Model_DegreeGroupMapper();
    					//$groupMapper->delete(new Application_Model_DegreeGroup(array('id'=>$data['select_degree_group'])));
    					// leave the form after save
    					//$this->_helper->redirector('degree-group-edit');
    				}
    				break;
    			case 'select':
    				if ($form->isValid ( $data ) ) {
    					$form->setGroupId($data['select_degree_group']);
    					$groupMapper = new Application_Model_DegreeGroupMapper();
    					$group = $groupMapper->find($data['select_degree_group']);
    					$form->showEdit($group->name);
    				}
    				break;
    			case 'save':
    				$data['select_degree_group'] = $data['select_degree_group_2'];
    				$form->setGroupId($data['select_degree_group']);
	    			$form->showEdit($data['select_degree_group']);
	    			if ($form->isValid ( $data ) ) {
	    				$group = new Application_Model_DegreeGroup(array('id'=>$data['select_degree_group'], 'name'=>$data['new_group_name']));
	    				$groupMapper = new Application_Model_DegreeGroupMapper();
	    				$groupMapper->update($group);
	    	
	    				// leave the form after save
	    				$this->_helper->redirector('degree-group-edit');
	    			}
	    			break;
    			}
    		}
    	
    	
    	$this->view->form = $form;
    }
    
    public function degreeGroupAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_degree_groups'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	
    	 	
    	 	
    }
    
    
    public function courseAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_course'))
    		$this->_helper->Redirector->setGotoSimple ( 'index' );
    	 
    	$autoElement = new Zend_Form_Element_Text('_course');
    	//$autoElement->setJQueryParam(
    	//		'source', '/exams-admin/ajax-course');
    	
    	$this->view->autoElmenet = $autoElement;
    	 
    }
    
    public function ajaxCourseAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_course'))
    		throw new Custom_Exception_PermissionDenied("Permission Denied");
    	 
    	$eqsq = new Application_Model_ExamQuickSearchQuery();
    	 
    	$results = $eqsq->getCourse($this->_getParam('term'));
    	$this->_helper->json($results);
    	 
    }
    
    public function ajaxConnectedCourseAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_course'))
    		throw new Custom_Exception_PermissionDenied("Permission Denied");
    
    	$eqsq = new Application_Model_ExamQuickSearchQuery();
    
    	$results = $eqsq->getConnectedCourse($this->_getParam('id'));
    	$this->_helper->json($results);
    
    }
    
    

    public function maintenanceAction()
    { 	
    	$request = $this->getRequest ();
    	if (isset ( $request->do )) {
    		$do = $request->do;
    			
    		switch ($do) {
    			case "checkInconsistency" :
    				
    				if($this->_authManager->isAllowed(null, 'maintenance_check_inconsistency')) {
    					$examMapper = new Application_Model_ExamMapper();
    					$examMapper->checkDatabaseForInconsistetExams();
    				} else { echo "Sorry, not allowed!"; }
    				break;
    			case "resetAllMimeTypes" :
    				if($this->_authManager->isAllowed(null, 'maintenance_determine_mime_types')) {
    					$docMapper = new Application_Model_ExamFileManager();
    					$docMapper->resetAllMimeTypesInDatabese();
    				} else { echo "Sorry, not allowed!"; }
    				break;
    			case "ckeckFiles" :
    				if($this->_authManager->isAllowed(null, 'maintenance_check_files_exist_and_readable')) {
    					$docMapper = new Application_Model_ExamFileManager();
    					$docMapper->checkAllFilesExistsAndReadable();
    				} else { echo "Sorry, not allowed!";
    				}
    				break;
    			case "checkExtention" :
    				if($this->_authManager->isAllowed(null, 'maintenance_check_files_extention')) {
    					$docMapper = new Application_Model_DocumentMapper();
    					$docMapper->checkDocumentExtentions();
    				} else { echo "Sorry, not allowed!";
    				}
    				break;
    			case "checkFileDamaged" :
    				if($this->_authManager->isAllowed(null, 'maintenance_check_damaged_files')) {
    					$docMapper = new Application_Model_ExamFileManager();
    					$docMapper->checkFilesMD5();
    				} else { echo "Sorry, not allowed!";
    				}
    				break;
    			case "generateMd5" :
    				if($this->_authManager->isAllowed(null, 'maintenance_generate_missing_md5sums')) {
    					$docMapper = new Application_Model_ExamFileManager();
    					$docMapper->restorMD5SumIfMising();
    				} else { echo "Sorry, not allowed!";
    				}
    				break;
    			case "sendTestMail" :
    				if($this->_authManager->isAllowed(null, 'maintenance_send_test_mail')) {
    					$noti = new Application_Model_Notification();
    					$noti->sendNotification("Test Mail from your Database", "This is a Test Mail.\nGenerated at: " . date('Y-m-d H:i:s')."\n\nHave a nice day!");
    					echo ("E-Mail was sent.");
    				} else { echo "Sorry, not allowed!";
    				}
    				break;
    		}
    	}
    } 
}

