<?php

class ExamsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->_helper->redirector('groups'); 
    }

    public function groupsAction()
    {
         $form = new Application_Form_DegreeGroups();
         
         $groups = new Application_Model_DegreeGroupMapper();
         $entries = $groups->fetchAll();
         
         $options = array();
         
         foreach($entries as $group)
         {
            $options[$group->getId()] = $group->getName();
         }
         
         $form->setMultiOptions($options);
         $this->view->form = $form;
    }

    public function degreesAction()
    {
        $degrees = new Application_Model_DegreeMapper();
        
        $form = new Application_Form_ExamDegrees();
    
        $request = $this->getRequest();
        //$formOrigin = new Application_Form_DegreeGroups();
 
        if ($this->getRequest()->isPost()) {
            // check if we get 2 submit parameter the group id und the submit button
            if (count($request->getPost()) == 2) {
            
                $post = $request->getPost();

                $entries = $degrees->fetchByGroup($post['group']);
                
                $options = array();
                
                foreach($entries as $group)
                {
                   $options[$group->getId()] = $group->getName();
                }
                $form->setMultiOptions($options);
                
                
            } else {
                return $this->_helper->redirector('groups');
            }
        } else {
            // to request (direct call)
            return $this->_helper->redirector('groups');
        }
 
        $this->view->form = $form;
    }

    public function coursesAction()
    {
        $courses = new Application_Model_CourseMapper();
        $lecturers = new Application_Model_LecturerMapper();
        $semesters = new Application_Model_SemesterMapper();
        $types = new Application_Model_ExamTypeMapper();
        
        $form = new Application_Form_ExamCourses();
    
        $request = $this->getRequest();
        //$formOrigin = new Application_Form_DegreeGroups();
 
        if ($this->getRequest()->isPost()) {
            // check if we get 2 submit parameter the group id und the submit button
            if (count($request->getPost()) == 2) {
            
                $post = $request->getPost();

                $entries = $courses->fetchByDegree($post['degree']);
                
                $options = array();
                
                foreach($entries as $group)
                {
                   $options[$group->getId()] = $group->getName();
                }
                $form->setCourseOptions($options);
                
                // set lecturers
                $entries = $lecturers->fetchByDegree($post['degree']);
                
                $options = array();
                
                foreach($entries as $group)
                {
                   $options[$group->getId()] = $group->getName();
                }
                $form->setLecturerOptions($options);
                
                
                // set semester
                $entries = $semesters->fetchAll();
                
                $options = array();
                
                foreach($entries as $group)
                {
                   $options[$group->getId()] = $group->getName();
                }
                $form->setSemesterOptions($options);
                
                
                // set types
                $entries = $types->fetchAll();
                
                $options = array();
                
                foreach($entries as $group)
                {
                   $options[$group->getId()] = $group->getName();
                }
                $form->setExamTypeOptions($options);
                
                
                
            } else {
                // wrong request (e.g. not the right number of variables)
                return $this->_helper->redirector('degrees');
            }
        } else {
            // no request
            return $this->_helper->redirector('degrees');
        }
 
        $this->view->form = $form;
    }

    public function searchAction()
    {
        /*  Example Parameters
            array (
              'controller' => 'exams',
              'action' => 'search',
              'module' => 'default',
              'course' => 
              array (
                0 => '1',
              ),
              'lecturer' => 
              array (
                0 => '1',
              ),
              'semester' => 
              array (
                0 => '62',
              ),
              'examType' => 
              array (
                0 => '-1',
              ),
              'submit' => 'Weiter',
            )
        */
        $exams = new Application_Model_ExamMapper();
        
        $request = $this->getRequest();
        $this->view->exams = array();
        
        if ($this->getRequest()->isPost()) {
            // check if we get 2 submit parameter the group id und the submit button
            if (count($request->getPost()) == 5) {
                
                $data = $request->getPost();
                
                //var_dump($data);
                
                $this->view->exams = $exams->fetch($data['course'],$data['lecturer'],$data['semester'],$data['examType']);
                
            } else {
                // ToDo: redcrict will fail, no parameter for courses
                return $this->_helper->redirector('courses');
            }
            
        }
        
    }


}



