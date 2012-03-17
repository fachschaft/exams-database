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
    }

}

