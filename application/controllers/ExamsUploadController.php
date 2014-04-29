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
				'course' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'lecturer' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				'semester' =>array(
						'filter' 	=> array('Int'),
						'validator' => array('Int')),
				
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
		$form->setAction('/exams-upload/degree');
		if ($this->getRequest()->isPost()) 
			if($form->isValid($this->getRequest()->getParams())) {
				$post = $this->getRequest ()->getParams ();
				return $this->_helper->Redirector->setGotoSimple('data', null, null, $post);
				}
		$this->view->form = $form;
				
	}
	public function dataAction() {
		// E.g. if the user for some reason has come here by a direct link, and therefore has no degree set, send him back to the beginning
		if (! isset ( $this->getRequest ()->degree ))
			$this->_helper->redirector ( 'index' );
		$degreeid = $this->getRequest ()->degree;
		$form = new Application_Form_UploadDetail ();
		$form->setMethod ( 'post' );
		$form->setAction ( '/exams-upload/data' );
		$form->setDegree ( $degreeid );
		
		$degree = new Application_Model_Degree(array('id'=> $degreeid));
		
		// Prepare the entries for populating
		$lecturers = new Application_Model_LecturerMapper();
		$entries['lecturer'] = $lecturers->fetchByDegree($degree);
		$courses = new Application_Model_CourseMapper();
		$entries['course'] = $courses->fetchByDegree($degree);
		$semesters = new Application_Model_SemesterMapper();
		$entries['semester'] = $semesters->fetchAll();
		$types = new Application_Model_ExamTypeMapper();
		$entries['type'] = $types->fetchAll();
		$courses = new Application_Model_ExamDegreeMapper();
		$entries['degree_exam'] = $courses->fetchAll();
		$types = new Application_Model_ExamSubTypeMapper();
		$entries['subType'] = $types->fetchAll();
		$courses = new Application_Model_ExamUniversityMapper();
		$entries['university'] = $courses->fetchAll();
		//populate.
		foreach(array_keys($entries) as $key)
		{
			$this->populateFields($form, $key, $entries[$key]);
		
		}

		if ($this->getRequest ()->isPost ()) {

			$post = $this->getRequest ()->getParams ();
		

			// We use the unescaped values (i.e. getInputUnescaped) for validation here to ensure that the form
			// returns the user input (and not the escaped input) if it is invalid
			if ($form->isValid ( $this->_filterManager->getInputUnescaped () )) {
				$post = $this->getRequest ()->getParams (); // for the insert we of course use escaped strings!!
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
				
				$examId = $examMapper->saveAsNewExam ( $exam );
				$exam->setId ( $examId );
				
				$data = array ();
				$data ['exam'] = $examId;
				return $this->_helper->Redirector->setGotoSimple ( 'files', null, null, $data );
			}
		}
		$this->view->form = $form;
		
	}
	
	public function filesAction() {
		// E.g. if the user for some reason has come here by a direct link, and therefore has no exam id set, send him back to the beginning
		if (! isset ( $this->getRequest ()->exam ))
			$this->_helper->redirector ( 'index' );
		// If user does not have permission to upload, deny permission
		if(!$this->_authManager->isAllowed(null, 'upload'))
			throw new Custom_Exception_PermissionDenied("Permission Denied");

		$config = Zend_Registry::get ( 'examDBConfig' );
		$form = new Application_Form_UploadFile ();
		$form->setExamId ( $this->getRequest ()->exam );
		
		$this->view->form = $form;
		
		
		$examMapper = new Application_Model_ExamMapper ();
		$config = Zend_Registry::get ( 'examDBConfig' );
		$dir = $config ['storagepath'];
		$form->setExamId ( $this->getRequest ()->exam );
		$form->setAction ( '/exams-upload/files/exam/' . $this->getRequest ()->exam );

			if ($form->isValid ( $this->getRequest ()->getParams () )) {

				$post = $this->getRequest ()->getParams ();
					
				$exam = $examMapper->findUpload ( $post ['exam'] );

					
				if ($form->exam_file->receive ()) {
					$fileManger = new Application_Model_ExamFileManager ();
					// save the received files
					$fileManger->storeUploadedFiles ( $form->exam_file->getFileName (), $post ['exam'] );
					// update exam to unhecked
					$examMapper->updateExamStatusToUnchecked ( $post ['exam'] );
					// redirect to final page
					$this->_helper->Redirector->setGotoSimple ( 'upload_final' );
				} 
			}
		
	}

	
	// This empty action is required to output a html page thanking user for his upload
	public function uploadfinalAction() {	}	
	
	private function parseEntries($entries)
	{
		$options = array();
		foreach($entries as $entry)
		{
			$options[$entry->getId()] = $entry->getName();
		}
		$opt = array();
		return $options;
	}
	
	public function populateFields($form, $element, $entries, $setSize = false)
	{
		$opt = $this->parseEntries($entries);
	

		$element = $form->getElement($element);
		$element->setMultiOptions($opt);
		if ($setSize) $element->setAttrib('size', count($opt));
	}
	
}
