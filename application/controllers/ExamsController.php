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
        $form = new Application_Form_DegreeGroups();
        $this->view->form = $form;
         
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $post = $this->getRequest()->getPost();
                if (isset($post['submit']))
                    unset($post['submit']);
                return $this->_helper->Redirector->setGotoSimple('degrees', null, null, $post);
            }
        }
    }

    public function degreesAction()
    {
        $form = new Application_Form_ExamDegrees();
        
        if ($this->getRequest()->isPost()) {
            $form->setMultiOptions($this->getRequest()->group);
            $form->setGroup($this->getRequest()->group);
            
            if($form->isValid($this->getRequest()->getPost())) {
                $post = $this->getRequest()->getPost();
                if (isset($post['submit']))
                    unset($post['submit']);
                if (isset($post['group']))
                    unset($post['group']);
                return $this->_helper->Redirector->setGotoSimple('courses', null, null, $post);
            }
        }
        
        if(isset($this->getRequest()->group)) {

            $form->setMultiOptions($this->getRequest()->group);
            $form->setGroup($this->getRequest()->group);
            $this->view->form = $form;
        } else {
            return $this->_helper->redirector('groups');
        }
    }

    public function coursesAction()
    {
        $form = new Application_Form_ExamCourses();

        // go back to group
        if(!isset($this->getRequest()->degree)) {
            return $this->_helper->redirector('groups');
        } else {
            //ToDo(aritas1): check if the degree is valid (db check)
        }
        
        //setup the form
        $form->setCourseOptions($this->getRequest()->degree);
        $form->setLecturerOptions($this->getRequest()->degree);
        $form->setDegree($this->getRequest()->degree);
        
        
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $post = $this->getRequest()->getPost();
                // remove parameter witch not be needed
                if (isset($post['submit']))
                    unset($post['submit']);
                if (isset($post['lecturer']) && in_array(-1, $post['lecturer']))
                    unset($post['lecturer']);
                if (isset($post['course']) && in_array(-1, $post['course']))
                    unset($post['course']);
                if (isset($post['semester']) && in_array(-1, $post['semester']))
                    unset($post['semester']);
                if (isset($post['examType']) && in_array(-1, $post['examType']))
                    unset($post['examType']);
                return $this->_helper->Redirector->setGotoSimple('search', null, null, $post);
            } else {
                $this->view->form = $form;
            }
        }
        
        $this->view->form = $form;
    }

    public function searchAction()
    {
        $request = $this->getRequest();
        $this->view->exams = array();
     
        // go back to degree
        if(!isset($this->getRequest()->degree)) {
            return $this->_helper->redirector('groups');
        } else {
            //ToDo(aritas1): check if the degree is valid (db check)
            // check also if the combination of degree / group is valid
        }
        
        if(!isset($this->getRequest()->course)) {
            $this->getRequest()->setParam('course', -1);
        }
        
        if(!isset($this->getRequest()->lecturer)) {
            $this->getRequest()->setParam('lecturer', -1);
        }
        
        if(!isset($this->getRequest()->semester)) {
            $this->getRequest()->setParam('semester', -1);
        }
        
        if(!isset($this->getRequest()->examType)) {
            $this->getRequest()->setParam('examType', -1);
        }
        
        $exams = new Application_Model_ExamMapper();
        $this->view->exams = $exams->fetch(
                        $this->getRequest()->course,
                        $this->getRequest()->lecturer,
                        $this->getRequest()->semester,
                        $this->getRequest()->examType, 
                        $this->getRequest()->degree,
						"3"	// 3 means public state
                        );
    }

    public function downloadAction()
    {
        if(isset($this->getRequest()->id)) 
        {        
            $x = new Application_Model_DocumentMapper();
            $entries = $x->fetch($this->getRequest()->id);
			
			$x->updateDownloadCounter($entries->id);
            
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=".date('YmdHis').".".$entries->getExtention());
            $config = Zend_Registry::get('examDBConfig');
            $path = $config['storagepath'];
            readfile ($path . $entries->getFileName() . "." . $entries->getextention());
            exit();
        } else {
            throw new Exception('Invalid document called', 500);
        }
    }

    public function uploadAction()
    {
        
        $form = null;
        $step = 1;
        
       
        if(isset($this->getRequest()->degree)) {
                $step = 2;
                $form = new Application_Form_UploadDetail();
                $form->setCourseOptions($this->getRequest()->degree);
                $form->setLecturerOptions($this->getRequest()->degree);
                $form->setDegree($this->getRequest()->degree);
        }
		
		if(isset($this->getRequest()->exam)) {
                $step = 3;
                $form = new Application_Form_UploadFile();
				$form->setExamId($this->getRequest()->exam);
        }
        
        if ($this->getRequest()->isPost() || $step == 3) {
            
            if(isset($this->getRequest()->step)) {
                $step = $this->getRequest()->step;
            }
            
            switch($step)
            {
                case 1:
                    $form = new Application_Form_UploadDegrees();
                    if($form->isValid($this->getRequest()->getPost())) {
                        $post = $this->getRequest()->getPost();
                        unset($post['submit']);
                        unset($post['step']);
                        $this->_helper->Redirector->setGotoSimple('upload', null, null, $post);
                    }
                    break;
                case 2:
                    $form = new Application_Form_UploadDetail();
                    $form->setCourseOptions($this->getRequest()->degree);
                    $form->setLecturerOptions($this->getRequest()->degree);
                    $form->setDegree($this->getRequest()->degree);
                    if($form->isValid($this->getRequest()->getPost())) {
                        $post = $this->getRequest()->getPost();
                        
                        // insert the new exam to into the database and mark the exam as not uploaded
                        $exam = new Application_Model_Exam();
                        $examMapper = new Application_Model_ExamMapper();
                        
                        $exam->setOptions($post);
                        $exam->setDegree(null);
                        $exam->setDegree($post['degree_exam']);
						$exam->setDegreeId($post['degree']);
                        
                        $examId = $examMapper->saveAsNewExam($exam);
                        $exam->setId($examId);
						
						$data = array();
						$data['exam'] = $examId;
						$this->_helper->Redirector->setGotoSimple('upload', null, null, $data);
                    }
                    break;
                case 3:
					$examMapper = new Application_Model_ExamMapper();
					if (!$this->getRequest()->isPost()) {
						$exam = $examMapper->find($this->getRequest()->exam);
						if($exam->id != $this->getRequest()->exam) {
							throw new Zend_Exception ("Sorry, no exam found.");
						}
						else if($exam->status != 1) {
							throw new Zend_Exception ("Sorry, you can't upload twice!");
						}
					}
					
                    $config = Zend_Registry::get('examDBConfig');
                    $dir = $config['storagepath'];
                    $form = new Application_Form_UploadFile();
					$form->setExamId($this->getRequest()->exam);
                    
					if ($this->getRequest()->isPost()) {
						if($form->isValid($this->getRequest()->getPost())) {
							$post = $this->getRequest()->getPost();
							
							$exam = $examMapper->find($post['examId']);
							if($exam->id != $post['examId'] || $exam->status != 1) {
								throw new Zend_Exception ("Sorry, you can't upload twice!");
							}

							if($form->exam_file->receive()) {
							$location = $form->exam_file->getFileName();
							//$mime = $form->exam_file->getMimeType();
							$ex_names = preg_split('/\./', $location, -1);
							$extention = $ex_names[count($ex_names)-1];
							
							if(!is_writable($dir)) {
								unlink($location);
								throw new Zend_Exception ("Cannot write in directory (".$dir.")");
							}
							//ToDo(aritas1): Handle all errors!
							$new_file_name = md5(time());
							$count = 0;
							while(file_exists($dir.$new_file_name.".".$extention))
							{
								$new_file_name = md5(time());
								$count++;
								if($count > 100) { throw new Zend_Exception ("Cannot find a free filname, please contact the admin!"); }
							}
							$sum = md5_file($location);
							
							// for php >= 5.3
							/*$finfo = new finfo(FILEINFO_MIME, "/usr/share/misc/magic"); // return mime type ala mimetype extension

							if (!$finfo) {
								throw new Zend_Exception ("Cannot open the mime type Database, please contact the admin!");
							}
							$mime = $finfo->file($location);*/
							$mime = mime_content_type($location);
							
							rename($location, $dir.$new_file_name.".".$extention);
							} else {
								break;
							}
							
							// save the file name to database (crate a document) and link this to the exam
							$document = new Application_Model_Document();

							$document->ExamId = $post['examId'];
							$document->extention = $extention;
							$file_names = preg_split('/\//', $location, -1);
							$document->submitFileName = $file_names[count($file_names)-1];
							$document->fileName = $new_file_name;
							$document->mimeType = $mime;
							$document->checkSum = $sum;
							
							$documentMapper = new Application_Model_DocumentMapper();
							$documentMapper->saveNew($document);
							
							
							$examMapper->updateExamStatusToUnchecked($document->ExamId);
							
							$this->_helper->Redirector->setGotoSimple('upload_final');
						}
                    }
                    break;
                default:
               
            }
            
        
        
        } else {
            if($form == null) $form = new Application_Form_UploadDegrees();
        }
        
        $this->view->form = $form;
    }

    public function uploadfinalAction()
    {
        // action body
    }


}



