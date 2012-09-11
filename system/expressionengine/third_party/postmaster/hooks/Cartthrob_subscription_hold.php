<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cartthrob_subscription_hold_postmaster_hook extends Base_hook {
	
	protected $title = 'CartThrob Subscription Hold';
	
	protected $cart;
	
	public function __construct()
	{
		parent::__construct();
		
		if(isset($this->EE->cartthrob))
		{
			$this->cart = $this->EE->cartthrob->cart;
		}
	}
	
	public function trigger($hook, $subscription = array())
	{		
		return parent::trigger($hook, $subscription, NULL);
	}
	
	public function post_process($responses = array())
	{
		// If end_script is TRUE, finish processing the order (taken directly from mod.cartthrob.php)
		if($this->end_script($responses))
		{
			$update = array('status' => 'hold');
			
			if ($error_message)
			{
				$update['error_message'] = $error_message;
			}
			
			if ($increment_rebill_attempts)
			{
				$update['rebill_attempts'] = $subscription['rebill_attempts'] + 1;
			}
			
			$this->EE->subscription_model->update($update, $subscription['id']);
		}
		
		return $responses;
	}
}