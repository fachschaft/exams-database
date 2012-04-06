<?php

class ExamsAdminController extends Zend_Controller_Action
{

    public function init()
    {
        // check if a login exists for admin controller
		if(!Zend_Auth::getInstance()->hasIdentity() && $this->getRequest()->getActionName() != "login") {
			$data = $this->getRequest()->getParams();
			// save the old controller and action to redirect the user after the login
			if(isset($data['rcontroller']) || isset($data['raction'])) { } else {
				$data['rcontroller'] = $data['controller'];
				$data['raction'] = $data['action'];
			}
			unset($data['controller']);
			unset($data['action']);
			$this->_helper->Redirector->setGotoSimple('login', null, null, $data);
		}
    }

    public function indexAction()
    {
        // action body
    }

    public function overviewAction()
    {
		$examMapper = new Application_Model_ExamMapper();
	
		$request = $this->getRequest();
        if(isset($request->do) && isset($request->id)) {
			$do = $request->do;
			$id = $request->id;
			
			switch($do)
            {
                case "approve":
					$examMapper->updateExamStatusToChecked($id);
					$this->_helper->Redirector->setGotoSimple('overview');
				break;
				case "disapprove":
					$examMapper->updateExamStatusToDisapprove($id);
					$this->_helper->Redirector->setGotoSimple('overview');
				break;
                case "delete":
					$examMapper->updateExamStatusToDelete($id);
					$this->_helper->Redirector->setGotoSimple('overview');
				break;
				case "edit":
					$data = array();
					$data['id'] = $request->id;
					$this->_helper->Redirector->setGotoSimple('editdetails', null, null, $data);
				break;
				case "edit-files":
					$data = array();
					$data['id'] = $request->id;
					$this->_helper->Redirector->setGotoSimple('editfiles', null, null, $data);
				break;
				case "log":
					$data = array();
					$data['id'] = $request->id;
					$this->_helper->Redirector->setGotoSimple('log', null, null, $data);
				break;
				default:
					throw new Zend_Exception ("Sorry, the do action (\"".$do."\") is not implemented.");
				break;
			}
		}
		
		
		
		$this->view->exams = $examMapper->fetchAdmin();
    }

    public function editdetailsAction()
    {
		if ($this->getRequest()->isPost()) {
			//save changes
			$form = new Application_Form_AdminDetail();
			$post = $this->getRequest()->getPost();
			$form->setAction('/exams-admin/editdetails/id/'.$post['exam_id']);
			$form->setCourseOptions($post['degree']);
			$form->setLecturerOptions($post['degree']);
			$form->setExamId($post['exam_id']);
			$form->setDegree($post['degree']);
			if($form->isValid($this->getRequest()->getPost())) {
				$exam = new Application_Model_Exam();
			    $exam->setId($post['exam_id'])
						->setCourse($post['course'])
						->setLecturer($post['lecturer'])
						->setSemester($post['semester'])
						->setType($post['type'])
						->setSubType($post['subType'])
						->setComment($post['comment'])
						->setUniversity($post['university'])
						->setAutor($post['autor'])
						->setDegree($post['degree_exam'])
					  ;
			    $examMapper = new Application_Model_ExamMapper();
				$examMapper->updateExam($exam);
				$this->_helper->Redirector->setGotoSimple('overview');
			} else { $this->view->form = $form; }
		} else {
		
			if(!isset($this->getRequest()->id)) {
				throw new Zend_Exception ("No id given.");
			}
			$id = $this->getRequest()->id;

			$examMapper = new Application_Model_ExamMapper();
			$exam = $examMapper->findAdmin($id);
			
			$form = new Application_Form_AdminDetail();
			$form->setAction('/exams-admin/editdetails/id/'.$id);
			$form->setExamId($exam->Id);
			$form->setDegree($exam->degreeId);
			$form->setCourseOptions($exam->degreeId, $exam->course);
			$form->setLecturerOptions($exam->degreeId, $exam->lecturer);
			$form->setSemesterOptions($exam->semester);
			$form->setExamTypeOptions($exam->type);
			$form->setExamSubType($exam->SubType);
			$form->setExamDegreeOptions($exam->degree);
			$form->setExamUniversityOptions($exam->University);
			$form->setExamComment($exam->comment);
			$form->setExamAutor($exam->autor);

			$this->view->form = $form;
		}
    }

    public function logAction()
    {
        // action body
		if(!isset($this->getRequest()->id)) {
		} else {
			$logMapper = new Application_Model_LogMapper();
			$log = $logMapper->fetchByExam($this->getRequest()->id);
			$this->view->log = $log->logMessages;
		}
    }

    public function loginAction()
    {
		$request = $this->getRequest();

        // Check if we have a POST request
        if ($request->isPost()) {
			// Get our form and validate it
			$form = $this->getLoginForm();
			if (!$form->isValid($request->getPost())) {
				// Invalid entries
				$this->view->form = $form;
				return $this->render('login'); // re-render the login form
			}

			// Get our authentication adapter and check credentials
			$adapter = $this->getAuthAdapter($form->getValues());
			$auth    = Zend_Auth::getInstance();
			$result  = $auth->authenticate($adapter);

			if (!$result->isValid()) {
				// Invalid credentials
				$form->setDescription('Invalid credentials provided');
				$this->view->form = $form;
				return $this->render('login'); // re-render the login form
			}

			// We're authenticated! Redirect to the page the user likeed to be or go to index page
			$data = $this->getRequest()->getParams();
			
			// reconstruct the old parameter
			unset($data['username']);
			unset($data['password']);
			unset($data['login']);
			
			if(!isset($data['raction'])) { $data['action'] = "index"; } else {  $data['action'] = $data['raction']; unset($data['raction']); }
			if(!isset($data['rcontroller'])) { $data['controller'] = null; } else {  $data['controller'] = $data['rcontroller']; unset($data['rcontroller']); }
			
			$this->_helper->Redirector->setGotoSimple($data['action'], $data['controller'], null, $data);
		}
		
		$this->view->form = $this->getLoginForm();
    }
	
	
	//////// Helper functions

    private function getLoginForm()
    {
		return new Application_Form_AdminLogin(array(
							'action' => '',
							'method' => 'post',
						));
    }

    private function getAuthAdapter(array $params)
    {
		// Set up the authentication adapter
		$config = Zend_Registry::get('authenticate');
		//return new Zend_Auth_Adapter_Digest($config['filename'], $config['realm'], $params['username'], $params['password']);
		//return new Custom_Auth_Adapter_InternetProtocol($params['username'], $params['password']);
		return new Custom_Auth_Adapter_Simple($params['username'], $params['password']);
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('login'); // back to login page
    }


}











