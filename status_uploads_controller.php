<?php
class StatusUploadsController extends AppController
{
	var $name = "StatusUploads";
	var $components = array("RequestHandler");
	var $helpers = array('Html','Javascript');
	var $uses = array('Login','StatusUpload');
	var $layout = "index3";
	
	function home()
	{
		$this->pageTitle = "IRIS : Reporting and Billing Solution : Work Status";
		$this->isLoggedIn();
		$this->set("menu",$this->StatusUpload->recMnu("",0));
		
	}
	 function upload_data()
	 {
		 $this->isLoggedIn();
		 if ($this->RequestHandler->isAjax())
		 {
			 $this->layout = "";
			 $this->set('date',date("Y-m-d",strtotime($this->params['url']['Date'])));
			 $this->set("worklist",$this->StatusUpload->worklist($this->params['url']['switch']));
			 $this->set("userlist",$this->StatusUpload->userlist($this->params['url']['switch']));
			 $this->set("switch",$this->params['url']['switch']);
			 $this->set("data",$this->StatusUpload->find_data($this->params['url']['Date'],$this->params['url']['switch']));
			
		 }
		 
		
	 }
	 
	 function datain()
	 {
		 //$this->isLoggedIn();
		 //$switch = $this->params['url']['Switch'];
		 //echo $switch;
		 $this->StatusUpload->insert_data($this->params['url']['Date'],
		 $this->params['url']['WorkName'],$this->params['url']['Frequency'],$this->params['url']['UserId'],$this->params['url']['Status'],
		 $this->params['url']['Switch']);
		 
	 }
	 
	
	
	function isLoggedIn()
	{
		if(!isset($_SESSION['SESS_USR_ID']))
		{
			$this->redirect(array('controller' => 'logins','action' => 'login'));
		}
	}

}
?>