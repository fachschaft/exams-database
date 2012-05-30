<?php

class Application_Model_FilterManager
{
	private $_allowedFileds;
	private $_inputFilter;
	private $_inputValidator;
	
	private $_inputUnescaped;
	
	/**
	 * @return the $_allowedFileds
	 */
	public function getAllowedFileds() {
		return $this->_allowedFileds;
	}

	/**
	 * @return the $_inputFilter
	 */
	public function getInputFilter() {
		return $this->_inputFilter;
	}

	/**
	 * @return the $_inputValidator
	 */
	public function getInputValidator() {
		return $this->_inputValidator;
	}

	/**
	 * @return the $_inputUnescaped
	 */
	public function getInputUnescaped() {
		return $this->_inputUnescaped;
	}

	/**
	 * @param field_type $_allowedFileds
	 */
	public function setAllowedFileds($_allowedFileds) {
		$this->_allowedFileds = $_allowedFileds;
	}

	public function setFilterAndValidator()
	{
		foreach ($this->_allowedFileds as $filed=>$options)
		{
			$this->_inputFilter[$filed] = $options['filter'];
			$this->_inputValidator[$filed] = $options['validator'];
		}
	}
	
	public function applyFilterAndValidators($request)
	{
		$allowedParms = array();
		foreach ($request->getParams() as $key=>$parm)
		{
			if(array_key_exists($key, $this->_allowedFileds))
			{
				$allowedParms[$key] = $parm;
			} else {
				if(isset($_GET[$key])) unset($_GET[$key]);
				if(isset($_POST[$key])) unset($_POST[$key]);
			}
		}
	
		// filter the given params
		$input = new Zend_Filter_Input(
				$this->_inputFilter,
				$this->_inputValidator,
				$allowedParms);
	
		$escaped = $input->getEscaped();
	
		$this->_inputUnescaped = $input->getUnescaped();
	
		$request->clearParams();
	
		$request->setParams($escaped);
	}


}

