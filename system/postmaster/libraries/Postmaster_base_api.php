<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Base_class.php';

abstract class Postmaster_base_api extends Base_class {
		
	/**
	 * API Class Title
	 * 
	 * @var string
	 */
	 		 	
	protected $title;
	
		
	/**
	 * Variable prefix
	 * 
	 * @var string
	 */
	 		 
	protected $var_prefix;
	
	
	/**
	 * Class suffix
	 * 
	 * @var string
	 */
	 		 
	protected $class_suffix;
	
	
	/**
	 * The API Class's filename
	 * 
	 * @var string
	 */
	 			
	protected $filename;
	
	
	/**
	 * Default Settings Array
	 * 
	 * @var string
	 */
	 		 
	protected $default_settings = array();
	
	
	/**
	 * Settings Array
	 * 
	 * @var string
	 */
	 		 
	protected $settings = array();
	
	
	/**
	 * Email Service
	 * 
	 * @var string
	 */
	 		 
	protected $service;
	
	
	/**
	 * Default Settings Field Schema
	 * 
	 * @var string
	 */
	 		 	 
	protected $fields = array();
	
	
	/**
	 * Fields to parse
	 * 
	 * @var string
	 */
	 		 
	protected static $parse_fields = array(
		'to_name',
		'to_email',
		'from_name',
		'from_email',
		'cc',
		'bcc',
		'subject',
		'message',
		'post_date_specific',
		'post_date_relative',
		'send_every',
		'extra_conditionals'
	);
	
	
	public function __construct($params = array())
	{
		parent::__construct($params);
				
		$this->EE =& get_instance();
		
		$this->EE->load->driver('interface_builder');
		
		$this->name         = strtolower(str_replace($this->class_suffix, '', get_class($this)));
		$this->filename     = ucfirst($this->name).'.php';
		$this->IB           = $this->EE->interface_builder;
		$this->channel_data = $this->EE->channel_data;	
								
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
	}
	
	
	/**
	 * Display the settings table
	 *
	 * @access	public
	 * @param	array 	The InterfaceBuilder schema array 
	 * @return	string
	 */
	 		
	public function display_settings($data = array())
	{
		if(count($this->fields) == 0)
		{		
			return FALSE;
		}
		
		$settings = isset($data->{$this->name}) ? $data->{$this->name} : $this->get_default_settings();
		
		$this->IB->set_var_name($this->name);
		$this->IB->set_prefix('setting');
		$this->IB->set_use_array(TRUE);
				
		return $this->IB->table($this->fields, $settings, postmaster_table_attr());
	}
	
	
	/**
	 * Instal
	 *
	 * @access	public
	 * @return	string
	 */
	 
	 public function install()
	 {
		return;
	 }
		 	
	
	/**
	 * Update
	 *
	 * @access	public
	 * @return	string
	 */
	 
	 public function update()
	 {
		 return;
	 }
	 	
	 		
	/**
	 * Parse the hook object
	 *
	 * @access	public
	 * @param	string 	The hook name
	 * @param	array 	An array of custom vars to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @return	
	 */
	 		
	public function parse($array, $vars = array(), $member_data = FALSE, $entry_data = array())
	{
		unset($array['settings']);
		
		$vars = $this->EE->channel_data->utility->add_prefix($this->var_prefix, $vars);
		
		if(!$member_data)
		{
			$member_data = $this->EE->postmaster_model->get_member(FALSE);
		}
		
		$member_data = $this->EE->channel_data->utility->add_prefix('member', $member_data);
				
		$vars = array_merge($member_data, $vars);
		
		return $this->EE->channel_data->tmpl->parse_array($array, $vars, $entry_data, FALSE, array(), $this->var_prefix.':');
	}
		
		
	/**
	 * Pre process allows devs to execute arbitrary logic before the 
	 * API class's email is sent.
	 *
	 * @access	public
	 * @param	array
	 * @return	null
	 */
	 	
	public function pre_process($vars = array())
	{
		return;
	}
	
	
	/**
	 * Post process allows devs to execute arbitrary logic after the 
	 * API class's email is sent.
	 *
	 * @access	public
	 * @param	array
	 * @return	null
	 */
	 		
	public function post_process($vars = array())
	{
		return;
	}
		
				
	/**
	 * This method is triggered after the variables have been parsed.
	 *
	 * @access	public
	 * @param	array 	An associative array of parsed values
	 * @return	array
	 */
	
	public function post_parse($parsed_vars = array())
	{
		return $parsed_vars;
	}
	
	
	/**
	 * Send boilerplate for each send method.
	 *
	 * @access	public
	 * @param	array	An array of custom variables to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @param	mixed 	An array of entry data. If empty, not fields are parsed
	 * @return	
	 */
	 
	abstract function send();
	
	
	/**
	 * Triggers the email to be sent
	 *
	 * @access	public
	 * @param	array	An array of custom variables to parse
	 * @param	mixed 	An array of member data. If FALSE, default is used
	 * @param	mixed 	If 'Undefined', NULL is returned, otherwise the
	 					the passed value is returned
	 * @return	
	 */
	 	
	public function trigger()
	{
		return $this->send();
	}
	
}