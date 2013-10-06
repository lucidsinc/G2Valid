<?php
/** 
* Name 			: 	G2Valid
* Release 		:	Alpha
* @author 		: 	Gayan Silva A.K.A G2 | Dsignvilla
* URL 			: 	http://dsignvilla.com
* Description	:	Simple Validation Class For Forms And Stuff
* Requirments	:	PHP 5.0 or Upwards
*/
class G2Valid
{
	// Holds The POST Information
	private $_post_details;
	// Holds The Validation Errors
	private $_errors = array();
	// Holds Pointer to post_details | Value
	private $_pointer;
	// Holds Pointer to post_details | Key
	private $_pointer_key;

	// ------------------------
	// 	Errors Codes Goes Here
	// ------------------------
	const E_IS_EMPTY 		= 	'Empty Field';
	const E_VALID_EMAIL		=	'Invalid Email';
	const E_VALID_URL		=	'Invalid URL';
	const E_ISSAME			=	"Fields Didn't Match";
	const E_VALID_IP		=	'Invalid Ip Address';
	const E_ALPHA			=	'Letters are only accepted';
	const E_ALPHANUIMERIC	=	'Numbers and letters are only accepted';
	const E_INT 			=	'Invalid Whole Number';
	const E_FLOAT 			=	'Invalid Float Number';
	const E_MINLENGTH		=	'Did Not Meet Minimum Length';
	const E_MAXLENGTH		=	'Maximum Length Exceeded';

	/**
	*	Checks for non empty and valid array
	*	@param array $post 	|	entire post or get array with values
	*/
	function __construct($post = array())
	{
		if(!is_array($post) || count($post) == 0)
		{
			trigger_error('POST Array should be passed with values');
		}

		$this->_post_details = $post;
	}

	/**
	*	Adding the Errors to the Array
	*	@param string $key 		|	Field Name
	*	@param string $defError 	|	Default Error Of The Field
	*	@param string $custError 	|	Custom Error Mesasge
	*/
	private function _report_error($key,$defError,$custError = NULL)
	{
		if(!isset($this->_errors[$key]))
		{
			$this->_errors[$key] = array();
		}

		array_push($this->_errors[$key],($custError != NULL) ?  $custError : $defError);
	}

	/**
	*	Check if field exists in form ( in array _post_details )
	*	@param string $field 	|	Field Name
	*	@return bool
	*/
	private function _checkField($field)
	{
		return array_key_exists($field,$this->_post_details) ? true : false;
	}

	/**
	*	Points to the field ( _post_details array key )
	*	@param string $field 	|	field name is sent here
	*/
	function field($field)
	{
		if($this->_checkField($field))
		{
			$this->_pointer = $this->_post_details[$field];
			$this->_pointer_key = $field;
			return $this;
		}

		throw new Exception("Invalid Field ".$field, 1);
	}

	/**
	*	Add a new field to form
	*	@param string $field 	|	field name to be created
	*/
	function addField($field)
	{
		if($this->_checkField($field))
		{
			throw new Exception('Field : "'.$field.'" Already exists', 2);
		}

		$this->_post_details[$field] = NULL;
		$this->_pointer_key = $field;
		
		return $this;
	}

	/**
	*	Set value for a field manually
	*	Ex : Create a dynamic value using addFeild Method save value
	*	$instance->addField('user_email')->setValue($db->getUser('email'))->checkEmail();
	*	@param string $field 	|	field name is sent here
	*	@param string $value 	|	$value for the object
	*/
	function setValue($value = NULL)
	{
		$this->_post_details[$this->_pointer_key] = $value;
		$this->_pointer = $this->_post_details[$this->_pointer_key];
		return $this;
	}

	/**
	*	Checks for any errors of the form
	*	@return boolean
	*/
	function validate()
	{
		if (!empty($this->_errors))
		{
		    return true;
		}

		return false;
	}

	/////////////////////////////////////////////////
	/////////// Error Reporting Methods ////////////
	///////////////////////////////////////////////

	/**
	*	Get Errors of a field as array
	*	@return field name with error as array
	*/
	function getErrorsAsArray()
	{
		if(array_key_exists($this->_pointer_key,$this->_errors))
		{
			return array("Field Name" => $this->_pointer_key,
				"Errors "=>$this->_errors[$this->_pointer_key]);
		}
	}

	/**
	*	Returns a single Error 	| the 1st error in the array for the field
	*	@param string $report 	| if set to true the field name will be returned
	*	@return string 
	*/
	function getError($report = NULL)
	{
		if(isset($this->_errors[$this->_pointer_key]))
		{
			return (isset($report) && $report == true) ? $this->_pointer_key.' | '.$this->_errors[$this->_pointer_key][0] : $this->_errors[$this->_pointer_key][0];
		}
	}

	/**
	*	All errors of a field (instance) as a string
	*	@return string 
	*/
	function getErrorAsString($report = NULL)
	{
		if(isset($this->_errors[$this->_pointer_key]))
		{
			$result = '';
			foreach ($this->_errors[$this->_pointer_key] as $key => $value) {
				$result .= (isset($report) && $report == true) ? $this->_pointer_key.' | '.$value."</br>" : $value."</br>";
			}
			return $result;
		}
	}

	/**
	*	Returns all errors of the instance
	*	@return array 
	*/
	function getAllErrors(){
		return $this->_errors;
	}

	/**
	*	Majic toString :D
	*/
	function __toString()
	{
		$this->getAllErrors();
	}

	/////////////////////////////////////////////////
	////////////// Validation Methods //////////////
	///////////////////////////////////////////////

	/**
	*	Checks for empty input
	*/
	function isEmpty($custError = NULL)
	{
		if(empty(trim($this->_pointer)))
		{
		   	$this->_report_error($this->_pointer_key,self::E_IS_EMPTY,$custError);
		}
		return $this;
	}

	/**
	*	Checks for valid email
	*/
	function validEmail($custError = NULL)
	{
		if(!filter_var($this->_pointer, FILTER_VALIDATE_EMAIL))
		{
			$this->_report_error($this->_pointer_key,self::E_VALID_EMAIL,$custError);
		}

		return $this;
	}

	/**
	*	Checks for valid URL
	*	@author : http://www.faqs.org/rfcs/rfc2396
	*/
	function validURL($custError = NULL)
	{
		if(!filter_var($this->_pointer, FILTER_VALIDATE_URL))
		{
			$this->_report_error($this->_pointer_key,self::E_VALID_URL,$custError);
		}

		return $this;
	}

	/**
	*	Checks if the $field2 is the same as the instace field
	*	Ex : $insatace->field('password')->isSame('passConfirm');
	*	@param string $field 	|	Field Name To be compared with
	*/
	function isSame($field2,$custError = NULL)
	{
		if($this->_checkField($field2))
		{
			if($this->_pointer != $this->_post_details[$field2])
			{
				$this->_report_error($this->_pointer_key,self::E_ISSAME,$custError);
			}

			return $this;
		}
		else
		{
			throw new Exception('Field : "'.$field2.'" does not exist', 3);
		}
	}

	/**
	*	Checks for valid IP , V4 And V6 Supported
	*/
	function validIP($custError = NULL)
	{
		if(!filter_var($this->_pointer, FILTER_VALIDATE_IP))
		{
			$this->_report_error($this->_pointer_key,self::E_VALID_IP,$custError);
		}

		return $this;
	}

	/**
	*	Check for Minimum Length of a string
	*	@param int $min the minimum string size
	*/
	function minLength($min,$custError = NULL)
	{
		if(strlen($this->_pointer) < $min)
		{
			$this->_report_error($this->_pointer_key,self::E_MINLENGTH,$custError);
		}
	}

	/**
	*	Check for Maximum Length of a string
	*	@param int $min the maximum string size
	*/
	function maxLength($max,$custError = NULL)
	{
		if(strlen($this->_pointer) > $max)
		{
			$this->_report_error($this->_pointer_key,self::E_MAXLENGTH,$custError);
		}
	}
	
	/////////////////////////////////////////////////
	//////// Validation For Text & Numbers /////////
	///////////////////////////////////////////////

	/**
     *	Checks for A-Za-z and space
     */
	function alpha($custError = NULL)
	{
		if(!ctype_alpha($this->_pointer))
		{
			$this->_report_error($this->_pointer_key,self::E_ALPHA,$custError);
		}

		return $this;
	}

	/**
     * Checks for A-Za-z 0-9
     */
	function alphanumeric($custError = NULL)
	{
		if(!ctype_alnum($this->_pointer))
		{
			$this->_report_error($this->_pointer_key,self::E_ALPHANUIMERIC,$custError);
		}

		return $this;
	}

	/**
     *	Checks for an valid int
     */
	function isInt($custError = NULL)
	{
		if(!ctype_digit((String) $this->_pointer))
		{
			$this->_report_error($this->_pointer_key,self::E_INT,$custError);
		}

		return $this;
	}

	/**
     *	Checks for an valid float
     */
	function isFloat($custError = NULL)
	{
		if(!filter_var($this->_pointer, FILTER_VALIDATE_FLOAT))
		{
			$this->_report_error($this->_pointer_key,self::E_FLOAT,$custError);
		}

		return $this;
	}
}
