<?php

class Application_Model_API
{	
	public function uncheckedExams($apiKey)
	{
		// check if api key ist valid
		if($apiKey != null && $this->validateKey($apiKey)) { 
			
		
		$examMapper = new Application_Model_ExamMapper();
		return $examMapper->countUnchecked();
		} else {
			exit();
		}
	}
	
	private function validateKey($apiKey)
	{
		return true;
	}

}

