<?php

class ExamsController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        $this->_helper->redirector('groups'); 
    }

    public function groupsAction()
    {
         $this->view->form = new Application_Form_DegreeGroups();
    }

    public function degreesAction()
    {
        $form = new Application_Form_ExamDegrees();
        $formOrigin = new Application_Form_DegreeGroups();
    
        if ($this->getRequest()->isPost()) {
            if($formOrigin->isValid($this->getRequest()->getPost())) {
            
                $post = $this->getRequest()->getPost();
                $form->setMultiOptions($post['group']);
                $form->setGroup($post['group']);
                $this->view->form = $form;
                
            } else {
                $this->view->form = $formOrigin;
            }
        } else {
            // to request (direct call)
            return $this->_helper->redirector('groups');
        }
    }

    public function coursesAction()
    {
        $form = new Application_Form_ExamCourses();
        $formOrigin = new Application_Form_ExamDegrees();
 
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
        
            //setup the origin form
            $formOrigin->setMultiOptions($post['group']);
            $formOrigin->setGroup($post['group']);
            
            if($formOrigin->isValid($this->getRequest()->getPost())) {
                
                $form->setCourseOptions($post['degree']);
                $form->setLecturerOptions($post['degree']);
                $form->setDegree($post['degree']);
                $this->view->form = $form;

            } else {
                $this->view->form = $formOrigin;
            }
        } else {
            // no request
            return $this->_helper->redirector('degrees');
        }
    }

    public function searchAction()
    {
        $formOrigin = new Application_Form_ExamCourses();
        
        $request = $this->getRequest();
        $this->view->exams = array();
        
        if ($this->getRequest()->isPost()) {
            $post = $request->getPost();
            $formOrigin->setCourseOptions($post['degree']);
            $formOrigin->setLecturerOptions($post['degree']);
            $formOrigin->setDegree($post['degree']);
            
            if($formOrigin->isValid($this->getRequest()->getPost())) {
                
                $data = $request->getPost();

                $exams = new Application_Model_ExamMapper();
                $this->view->exams = $exams->fetch($data['course'],$data['lecturer'],$data['semester'],$data['examType'], $data['degree']);
                
            } else {
                $this->view->form = $formOrigin;
            }
        }
    }
}



