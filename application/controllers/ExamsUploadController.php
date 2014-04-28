<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.1
 * @since		1.1
 * @todo		-
 */

class ExamsUploadController extends Zend_Controller_Action {
	
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
				
				// Degree select 
				'degree' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				

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
		
		// check if the current time has past the last semester
		$semesterMapper = new Application_Model_SemesterMapper();
		$semesterMapper->checkFuthereSemesterExists();

	}
	
	//Index action is called after every form and forwards to the next form required
	public function indexAction() {


		$this->_helper->redirector ( 'degree' );
	}
	
	
	public function degreeAction() {

		$form = new Application_Form_UploadDegrees ();
		$form->setMethod('post');
		$form->setAction('/exams-upload/data');
		$this->view->form = $form;
		
	}
	
	public function uploadAction() {
		if(!$this->_authManager->isAllowed(null, 'upload'))
			throw new Custom_Exception_PermissionDenied("Permission Denied");
		
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
}
