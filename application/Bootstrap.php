<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    
    public function _initExams()
    {
        $examsConfig = $this->getOption('exams');
        Zend_Registry::set('examDBConfig', $examsConfig);
		
		$authenticateConfig = $this->getOption('authenticate');
        Zend_Registry::set('authenticate', $authenticateConfig);
    }
    
    public function _initPlaintextHelper()
    {
        // Initialise Zend_Layout's MVC helpers
        Zend_Layout::startMvc();
        Zend_Layout::getMvcInstance()->getView()->addHelperPath('Custom/View/Helper/', 'Custom_View_Helper');
    }
    
    protected function _initTimezone()
    {
    	date_default_timezone_set('UTC');
    }

}

