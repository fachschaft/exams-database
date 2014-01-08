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
    				$this->view->id = $id;
    				$this->view->edit_exam = $request->edit_exam;
    				break;
    			case "delete_files_do" :
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
    				
    				
    		}
    	}
    } 

    public function statisticsUploadAction()
    {
    	$stats = new Application_Model_Statistics();
    	
    	// define years
    	$this->view->upload_years = $stats->getAllUsedYears();

    	
    	$this->view->upload_degrees = $stats->getAllDegrees();
    	
    	$this->view->upload_groups = $stats->getAllDegreeGroups();
    	
    	
    }
    
    public function statisticsUploadTotalAction()
    {    	
    	// this function is for ajax polling
    	
    	require_once ('jpgraph/jpgraph.php');
    	
    	$stats = new Application_Model_Statistics();
    	 
    	// define years
    	$this->view->upload_years = $stats->getAllUsedYears();
    
    	 
    	$this->view->upload_degrees = $stats->getAllDegrees();
    	 
    	$this->view->upload_groups = $stats->getAllDegreeGroups();
    	
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    	
    	$group = false;
    	$degree = -1;
    	
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    	
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    	
    	$months = $gDateLocale->GetShortMonth();
    	$data1y = $stats->getExamUploadsYear($year, $degree, $group);
    	
    	for ($i = 0; $i < sizeof($data1y); $i++) {
    		if($data1y[$i] == "") { $data1y[$i] = 0; }
    		echo($months[$i] . " - " . $data1y[$i] . "<br>");
    	}
    	
    	exit();
    	 
    	 
    }
    
    public function statisticsGraphUploadTotal2Action()
    {
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    	
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_bar.php');
    	require_once ('jpgraph/jpgraph_line.php');
    	
    	//bar1
    	$data1y=array(115,130,135,130,110,130,130,150,130,130,150,120);
    	//bar2
    	$data2y=array(180,200,220,190,170,195,190,210,200,205,195,150);
    	//bar3
    	$data3y=array(220,230,210,175,185,195,200,230,200,195,180,130);
    	$data4y=array(40,45,70,80,50,75,70,70,80,75,80,50);
    	$data5y=array(20,20,25,22,30,25,35,30,27,25,25,45);
    	//line1
    	$data6y=array(50,58,60,58,53,58,57,60,58,58,57,50);
    	foreach ($data6y as &$y) { $y -=10; }
    	
    	// Create the graph. These two calls are always required
    	$graph = new Graph(750,320,'auto');
    	$graph->SetScale("textlin");
    	$graph->SetY2Scale("lin",0,90);
    	$graph->SetY2OrderBack(false);
    	
    	$graph->SetMargin(35,50,20,5);
    	
    	$theme_class = new UniversalTheme;
    	$graph->SetTheme($theme_class);
    	
    	//$graph->yaxis->SetTickPositions(array(0,50,100,150,200,250,300,350), array(25,75,125,175,275,325));
    	//$graph->y2axis->SetTickPositions(array(30,40,50,60,70,80,90));
    	
    	$months = $gDateLocale->GetShortMonth();
    	$months = array_merge(array_slice($months,3,9), array_slice($months,0,3));
    	$graph->SetBox(false);
    	
    	$graph->ygrid->SetFill(false);
    	$graph->xaxis->SetTickLabels(array('A','B','C','D'));
    	$graph->yaxis->HideLine(false);
    	$graph->yaxis->HideTicks(false,false);
    	// Setup month as labels on the X-axis
    	$graph->xaxis->SetTickLabels($months);
    	
    	// Create the bar plots
    	$b1plot = new BarPlot($data1y);
    	$b2plot = new BarPlot($data2y);
    	
    	$b3plot = new BarPlot($data3y);
    	$b4plot = new BarPlot($data4y);
    	$b5plot = new BarPlot($data5y);
    	
    	//$lplot = new LinePlot($data6y);
    	
    	// Create the grouped bar plot
    	$gbbplot = new AccBarPlot(array($b3plot,$b4plot,$b5plot));
    	//$gbplot = new GroupBarPlot(array($b1plot,$b2plot,$gbbplot));
    	
    	// ...and add it to the graPH
    	$graph->Add($gbbplot);
    	//$graph->AddY2($lplot);
    	
    	$b1plot->SetColor("#0000CD");
    	$b1plot->SetFillColor("#0000CD");
    	$b1plot->SetLegend("Cliants");
    	
    	$b2plot->SetColor("#B0C4DE");
    	$b2plot->SetFillColor("#B0C4DE");
    	$b2plot->SetLegend("Machines");
    	
    	$b3plot->SetColor("#8B008B");
    	$b3plot->SetFillColor("#8B008B");
    	$b3plot->SetLegend("First Track");
    	
    	$b4plot->SetColor("#DA70D6");
    	$b4plot->SetFillColor("#DA70D6");
    	$b4plot->SetLegend("All");
    	
    	$b5plot->SetColor("#9370DB");
    	$b5plot->SetFillColor("#9370DB");
    	$b5plot->SetLegend("Single Only");
    	
    	/*$lplot->SetBarCenter();
    	$lplot->SetColor("yellow");
    	$lplot->SetLegend("Houses");
    	$lplot->mark->SetType(MARK_X,'',1.0);
    	$lplot->mark->SetWeight(2);
    	$lplot->mark->SetWidth(8);
    	$lplot->mark->setColor("yellow");
    	$lplot->mark->setFillColor("yellow");*/
    	
    	$graph->legend->SetFrameWeight(1);
    	$graph->legend->SetColumns(6);
    	$graph->legend->SetColor('#4E4E4E','#00A78A');
    	
    	/*$band = new PlotBand(VERTICAL,BAND_RDIAG,11,"max",'khaki4');
    	$band->ShowFrame(true);
    	$band->SetOrder(DEPTH_BACK);
    	$graph->Add($band);*/
    	
    	$graph->title->Set("Combineed Line and Bar plots");
    	
    	// Display the graph
    	$graph->Stroke();
    	
    	exit();
    }

    public function statisticsGraphUploadTotalAction()
	{
		$stats = new Application_Model_Statistics();
			
		
		$path = '../library/jpgraph';
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		
    	require_once ('jpgraph/jpgraph.php');
		require_once ('jpgraph/jpgraph_bar.php');
		
		
		
		
		$request = $this->getRequest ();
		if (isset ( $request->year )) {
			$year = $request->year;
		} else {
			$year = date("Y");
		}
		
		$group = false;
		$degree = -1;
		
		if (isset ( $request->group )) {
			$degree=$request->group;
			$group = true;
		}
		
		if (isset ( $request->degree )) {
			$degree = $request->degree;
		}
		
		$months = $gDateLocale->GetShortMonth();
		$data1y = $stats->getExamUploadsYear($year, $degree, $group);
		
		
		//$data1y=array(47,80,40,116);
		$data2y=array(61,30,82,105);
		$data3y=array(115,50,70,93);
		
		
		// Create the graph. These two calls are always required
		$graph = new Graph(700,300,'auto');
		$graph->SetScale("textint");
		
		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		
		//$graph->yaxis->SetTickPositions(array(0,30,60,90,120,150), array(15,45,75,105,135));
		$graph->SetBox(false);
		
		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($months);
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		
		// Create the bar plots
		$b1plot = new BarPlot($data1y);
		
		// Create the grouped bar plot
		$gbplot = new GroupBarPlot(array($b1plot));
		// ...and add it to the graPH
		$graph->Add($gbplot);
		
		
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#cc1111");
		
		/*$b2plot->SetColor("white");
		$b2plot->SetFillColor("#11cccc");
		
		$b3plot->SetColor("white");
		$b3plot->SetFillColor("#1111cc");*/
		$sum = 0;
		foreach ($data1y as $i) {
			$sum += $i;
		}
		$title = $year . " // uploads // total: " . $sum;
		if($degree != -1) {
			if($group) {
				$amd = new Application_Model_DegreeGroupMapper();
				$group = $amd->find($degree);
				$title .= "\n group: " . $group->getName();
			} else {
				$amd = new Application_Model_DegreeMapper();
				$degree = $amd->find($degree);
				$title .= "\n degree: " . $degree->getName();
			}
		}
		$graph->title->Set($title);
		
		// Display the graph
		$graph->Stroke();
    
    	exit();
    }
    
    public function statisticsGraphUploadTotalTypesAction()
    {
    	$stats = new Application_Model_Statistics();
    		
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_bar.php');
    
    
    
    
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    
    	$group = false;
    	$degree = -1;
    
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    
    	$months = $gDateLocale->GetShortMonth();
    	$results = $stats->getExamUploadsYearByType($year, $degree, $group);
    
    	
    
    	  
    
    	// Create the graph. These two calls are always required
    	$graph = new Graph(700,300,'auto');
    	$graph->SetScale("textint");
    
    	$theme_class=new UniversalTheme;
    	$graph->SetTheme($theme_class);
    
    	//$graph->yaxis->SetTickPositions(array(0,30,60,90,120,150), array(15,45,75,105,135));
    	$graph->SetBox(false);
    
    	$graph->ygrid->SetFill(false);
    	$graph->xaxis->SetTickLabels($months);
    	$graph->yaxis->HideLine(false);
    	$graph->yaxis->HideTicks(false,false);
    

    	
    	
    	
    	$gbbplot_array = array();
    	$amey = new Application_Model_ExamTypeMapper();
    	$all = $amey->fetchAll();
    	
    	// add all months upload in plain format
    	foreach ($results as $type_id => $type) {
    		$a = array();
    		foreach ($type as $month) {
    			$a[] = $month['uploads'];
    		}
    		$bplot = new BarPlot($a);
    		$bplot->SetLegend($amey->find($type_id)->getName());
    		$gbbplot_array[] = $bplot;
    	}
    	
    		
    	// Create the grouped bar plot
    	$gbbplot = new AccBarPlot($gbbplot_array);
    	$gbplot = new GroupBarPlot(array($gbbplot));
    	
    	
    	$graph->Add($gbplot);
    	
   	
    	$sum = 0;
    	foreach ($results as $typs) {
    		foreach ($typs as $month) {
    			$sum += $month['uploads'];
    		}
    		
    	}
    	$title = $year . " // uploads // total: " . $sum;
    	if($degree != -1) {
    		if($group) {
    			$amd = new Application_Model_DegreeGroupMapper();
    			$group = $amd->find($degree);
    			$title .= "\n group: " . $group->getName();
    		} else {
    			$amd = new Application_Model_DegreeMapper();
    			$degree = $amd->find($degree);
    			$title .= "\n degree: " . $degree->getName();
    		}
    	}
    	$graph->title->Set($title);
    
    	// Display the graph
    	$graph->Stroke();
    
    	exit();
    }
    
    public function statisticsGraphUploadTotalPieGroupsAction()
    {
    	$stats = new Application_Model_Statistics();
    		
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
		require_once ('jpgraph/jpgraph_pie.php');
		
		$request = $this->getRequest ();
		if (isset ( $request->year )) {
			$year = $request->year;
		} else {
			$year = date("Y");
		}
	
		$data1y = $stats->getExamAllGroupsUploads($year);
    
    	$data = $data1y;
    	
    	// Create the Pie Graph.
    	$graph = new PieGraph(350,380);
    	
    	$theme_class="DefaultTheme";
    	//$graph->SetTheme(new $theme_class());
    	
    	// Set A title for the plot
    	$graph->title->Set("Distribution over degree groups");
    	$graph->SetBox(true);
    	
    	// Create
    	$p1 = new PiePlot($data);
    	$graph->Add($p1);
    	
    	$axis = array();
    	foreach ($stats->getAllDegreeGroups() as $elemet) {
    		$axis[] = $elemet['name'];
    	}
    	$p1->SetLegends($axis);
    	//$graph->legend->SetMargin(20,5);
    	
    	

		$graph->legend->SetPos(0.5,0.97,'center','bottom');
		$graph->legend->SetColumns(1);
    	
    	//$graph->SetLegends();
    	
    	$p1->ShowBorder();
    	$p1->SetColor('black');
    	//$p1->SetSliceColors(array('#1E90FF','#2E8B57','#ADFF2F','#DC143C','#BA55D3'));
    	$graph->Stroke();
    
    
    	
    
    	exit();
    }
    
    public function statisticsGraphUploadTotalPieDegreesAction()
    {
    	$stats = new Application_Model_Statistics();
    
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_pie.php');
    
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    
    	// Some data and the labels
    	$data   = $stats->getExamAllDegreesUploads($year);
    	$labels = $stats->getAllDegrees();
    	
    	$new_data = array();
    	foreach ($data as $i => $dat) {
    		if($dat != 0) {
    			$new_data[] = array('data' => $dat, 'lable' => $labels[$i]['name'] );
    			
    		}
    	}
    	
    	
    	$labels = array();
    	foreach ($new_data as $lable) {
    		$labels[] = substr($lable['lable'], 0, 30)." (%.1f%%)";
    	}
    	
    	$data = array();
    	foreach ($new_data as $dat) {
    		$data[] = $dat['data'];
    	}

    	
    	// Create the Pie Graph.
    	$graph = new PieGraph(700,380, "auto");
    	$graph->SetShadow();
    	
    	// Set A title for the plot
    	$graph->title->Set('Distribution over degrees');
    	$graph->title->SetColor('black');
    	
    	// Create pie plot
    	$p1 = new PiePlot($data);
    	$p1->SetCenter(0.5,0.5);
    	$p1->SetSize(0.3);
    	
    	// Enable and set policy for guide-lines. Make labels line up vertically
    	$p1->SetGuideLines(true,false);
    	$p1->SetGuideLinesAdjust(1.1);
    	
    	// Setup the labels to be displayed
    	$p1->SetLabels($labels);
    	
    	// This method adjust the position of the labels. This is given as fractions
    	// of the radius of the Pie. A value < 1 will put the center of the label
    	// inside the Pie and a value >= 1 will pout the center of the label outside the
    	// Pie. By default the label is positioned at 0.5, in the middle of each slice.
    	$p1->SetLabelPos(1);
    	
    	// Setup the label formats and what value we want to be shown (The absolute)
    	// or the percentage.
    	$p1->SetLabelType(PIE_VALUE_PER);
    	$p1->value->Show();
    	$p1->value->SetColor('black');
    	
    	// Add and stroke
    	$graph->Add($p1);
    	$graph->Stroke();
    
    
    	exit();
    }
    
    public function statisticsDownloadAction()
    {
    	$stats = new Application_Model_Statistics();
    	 
    	// define years
    	$this->view->upload_years = $stats->getAllUsedYears();
    
    	 
    	$this->view->upload_degrees = $stats->getAllDegrees();
    	 
    	$this->view->upload_groups = $stats->getAllDegreeGroups();
    	
    	 
    	 
    }
    
    
    public function statisticsAjaxDownloadRankingAction()
    {
    	
    	//if(!$this->_authManager->isAllowed(null, 'modify_course'))
    	//	throw new Custom_Exception_PermissionDenied("Permission Denied");
    	
    	$stats = new Application_Model_Statistics();
    	
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    	 
    	$group = false;
    	$degree = -1;
    	 
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    	 
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    	
    	$page = 1;
    	$max_elements = 30;
    	if (isset ( $request->elements )) {
    		$max_elements=$request->elements;
    	}
    	
    	if (isset ( $request->page )) {
    		$page = $request->page;
    	}
    	
    
    	$results = $stats->getExamDownloadsRankingYear($year, $degree, $group);
    	

    	$results2 = array_slice($results, ($page-1) * $max_elements, $max_elements);
    	
    	//var_dump($results);
    	
    	$em = new Application_Model_ExamMapper();
    	$exams = array();
    	
    	//var_dump($em->find(1337));
    	$ids = array();
    	
    	foreach ($results2 as $exam) {
    		$ids[] = $exam['idexam'];
    		
    	}
    	
    	//var_dump($ids);
    	//die();
    	
    	
    	$res = $em->fetchQuick(-1, -1, -1, -1, -1, array(), true, $ids);
    	
    	//var_dump($res);
    		
    	foreach ($res as $ex) {
    		$cors = array();
    		foreach ($ex->getCourse() as $cor) {
    			$cors[] = $cor->getName();
    		}
    		$ccors = array();
    		foreach ($ex->getCourseConnected() as $cor) {
    			$ccors[] = $cor->getName();
    		}
    		$lect = array();
    		foreach ($ex->getLecturer() as $cor) {
    			$lect[] = $cor->getName() . ", " . $cor->getDegree() . " " . $cor->getFirstName();
    		}
    		$files = array();
    		foreach ($ex->getDocuments() as $cor) {
    			//$files[] = array('name' => $cor->getDisplayName().".".$cor->getExtention(), 'id'=>$cor->getId());
    			$files[] = array('name' => ".".$cor->getExtention(), 'id'=>$cor->getId());
    			 
    		}
    		
    		$rank = -1;
    		$downlow = -1;
    		foreach ($results2 as $element) {
    			if($element['idexam'] == $ex->getId()) {
    				$rank = $element['rank'];
    				$downlow = $element['downloads'];
    			}
    		}
    		$exams[] = array(
    				'idexam' =>$ex->getId(),
    				'downloads' => $downlow,
    				'rank' => $rank,
    				'comment' =>$ex->getComment(),
    				'course' => $cors,
    				'course_connected' => $ccors,
    				'degree'=> $ex->getDegree()->getName(),	
    				'semester'=> $ex->getSemester()->getName(),
    				'lecturer'=> $lect,
    				'type'=> $ex->getType()->getName(),
    				'sub_type'=> $ex->getSubType()->getName(),
    				'semester'=> $ex->getSemester()->getName(),
    				'autor'=> $ex->getAutor(),
    				'files' => $files,
    				'uni' => $ex->getUniversity()->getName(),
    		);
    	}
    	
    	// define a custom month sort
    	function cmp($a, $b)
    	{
    		if ($a['rank'] == $b['rank']) {
    			return 0;
    		}
    		return ($a['rank'] < $b['rank']) ? -1 : 1;
    	}
    	
    	usort($exams, "cmp");
    	
    	
    	/*
    	 * object(Application_Model_Exam)#102 (16) {
  ["_id":protected]=>
  int(1337)
  ["_autor":protected]=>
  string(0) ""
  ["_comment":protected]=>
  string(0) ""
  ["_created":protected]=>
  int(1)
  ["_modified":protected]=>
  int(1381701152)
  ["_degree":protected]=>
  object(Application_Model_Degree)#103 (3) {
    ["_degree_group":protected]=>
    NULL
    ["_name":protected]=>
    string(6) "Physik"
    ["_id":protected]=>
    int(7)
  }
  ["_status":protected]=>
  object(Application_Model_ExamStatus)#104 (2) {
    ["_name":protected]=>
    string(6) "public"
    ["_id":protected]=>
    string(1) "3"
  }
  ["_semester":protected]=>
  object(Application_Model_Semester)#105 (3) {
    ["_name":protected]=>
    string(7) "SS 2006"
    ["_id":protected]=>
    int(62)
    ["_begin_time":protected]=>
    NULL
  }
  ["_type":protected]=>
  object(Application_Model_ExamType)#106 (2) {
    ["_name":protected]=>
    string(7) "Klausur"
    ["_id":protected]=>
    int(1)
  }
  ["_subType":protected]=>
  object(Application_Model_ExamSubType)#107 (2) {
    ["_name":protected]=>
    string(18) "ohne L&ouml;sungen"
    ["_id":protected]=>
    int(1)
  }
  ["_university":protected]=>
  object(Application_Model_ExamUniversity)#108 (2) {
    ["_name":protected]=>
    string(12) "Uni Freiburg"
    ["_id":protected]=>
    int(1)
  }
  ["_writtenDegree":protected]=>
  object(Application_Model_ExamDegree)#109 (2) {
    ["_name":protected]=>
    string(9) "Diplom NF"
    ["_id":protected]=>
    int(3)
  }
  ["_lecturer":protected]=>
  array(1) {
    [0]=>
    object(Application_Model_Lecturer)#118 (5) {
      ["_degree":protected]=>
      string(9) "Prof. Dr."
      ["_firstName":protected]=>
      string(2) "A."
      ["_name":protected]=>
      string(6) "Blumen"
      ["_id":protected]=>
      int(80)
      ["_degrees":protected]=>
      NULL
    }
  }
  ["_course":protected]=>
  array(1) {
    [0]=>
    object(Application_Model_Course)#112 (4) {
      ["_name":protected]=>
      string(15) "Quantenmechanik"
      ["_id":protected]=>
      int(208)
      ["_degrees":protected]=>
      NULL
      ["_connectedCourse":protected]=>
      NULL
    }
  }
  ["_courseConnected":protected]=>
  array(1) {
    [0]=>
    object(Application_Model_Course)#115 (4) {
      ["_name":protected]=>
      string(44) "Quantum mechanics for micro and nano systems"
      ["_id":protected]=>
      int(232)
      ["_degrees":protected]=>
      NULL
      ["_connectedCourse":protected]=>
      NULL
    }
  }
  ["_documents":protected]=>
  array(1) {
    [0]=>
    object(Application_Model_Document)#121 (14) {
      ["_extention":protected]=>
      string(3) "zip"
      ["_id":protected]=>
      int(1630)
      ["_examId":protected]=>
      int(1337)
      ["_exam":protected]=>
      NULL
      ["_uploadDate":protected]=>
      string(19) "2013-10-13 21:51:19"
      ["_deleted":protected]=>
      bool(false)
      ["_fileName":protected]=>
      string(36) "0e76470b210350568519f8790841e5fd.zip"
      ["_displayName":protected]=>
      string(11) "Quant_Blume"
      ["_mimeType":protected]=>
      string(15) "application/zip"
      ["_submitFileName":protected]=>
      string(15) "Quant_Blume.zip"
      ["_checkSum":protected]=>
      NULL
      ["_reviewed":protected]=>
      NULL
      ["_downloads":protected]=>
      NULL
      ["_collection":protected]=>
      string(1) "0"
    }
  }
}
    	 */
    	
    	
    	$this->_helper->json($exams);
    	
    	exit();
    	 
    
    
    }
    
    public function statisticsGraphDownloadAction()
    {
    	$stats = new Application_Model_Statistics();
    
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_scatter.php');
    	require_once ('jpgraph/jpgraph_bar.php');
    	require_once ('jpgraph/jpgraph_line.php');
    	
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    	
    	$group = false;
    	$degree = -1;
    	
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    	
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    	
    	//$months = $gDateLocale->GetShortMonth();
    	$results = $stats->getExamDownloadsDailyYear($year, $degree, $group);
    	
    	//var_dump($results);
    	//die();
    	
    	$days = array();
    	$downloads = array();
    	$total_downloads = 0;
    	foreach ($results as $day=>$download)
    	{
    		//echo($day);
    		$days[] = $day;
    		$downloads[] = $download;
    		$total_downloads += $download;
    	}
    	//die();
    	
    	$datay = $downloads;//array(3.5,3.7,3,4,6.2,6,3.5,8,14,8,11.1,13.7);
    	$datax = $days;//array(20,22,12,13,17,20,16,19,30,31,40,43);
    	$graph = new Graph(900,380);
    	$graph->img->SetMargin(40,40,40,40);
    	$graph->img->SetAntiAliasing();
    	$graph->SetScale("linlin");
    	$graph->SetShadow();
    	
    	//$graph->yaxis->SetTickPositions(array(0,50,100,150,200,250,300,350), array(25,75,125,175,275,325));
    	//$graph->y2axis->SetTickPositions(array(30,40,50,60,70,80,90));

    	$markings = array();
    	for ($i = 0; $i < 365; $i++) {
    		if($i%15 == 0 and $i%30 != 0) {
    			$markings[] = $i;
    		}
    	}
    	
    	$months = $gDateLocale->GetShortMonth();
    	
    	$graph->xaxis->SetTickPositions($markings, NULL, $months);
    	
    	
    	/*$data6y=array(50,58,60,58,53,58,57,60,58,58,57,50);
    	 
    	$lplot = new LinePlot($data6y);
    	 
    	$graph->Add($lplot);*/
   	
    	
    	$title = ($year . " // Downloads // total: ". $total_downloads);
    	if($degree != -1) {
    		if($group) {
    			$amd = new Application_Model_DegreeGroupMapper();
    			$group = $amd->find($degree);
    			$title .= "\n group: " . $group->getName();
    		} else {
    			$amd = new Application_Model_DegreeMapper();
    			$degree = $amd->find($degree);
    			$title .= "\n degree: " . $degree->getName();
    		}
    	}
    	
    	$graph->title->Set($title);
    	//$graph->title->SetFont(FF_FONT1,FS_BOLD);
    	
    	$band = new PlotBand(VERTICAL,BAND_RDIAG,"min","max",'khaki4');
    	$band->ShowFrame(true);
    	$band->SetOrder(DEPTH_BACK);
    	$graph->Add($band);
    	
    	
    	$b1plot = new BarPlot($datay);
    	$gbplot = new GroupBarPlot(array($b1plot));
    	
    	/*$sp1 = new ScatterPlot($datay,);
    	$sp1->SetLinkPoints(true,"red",2);
    	$sp1->mark->SetType(MARK_FILLEDCIRCLE);
    	$sp1->mark->SetFillColor("navy");
    	$sp1->mark->SetWidth(0);*/
    	
    	$graph->Add($gbplot);
    	$graph->Stroke();
    	
    	exit();
    }

}

