<?php

class ExamsAdminController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
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


}





