<?php
class SetDatabase extends CI_Controller{
/*	function SetDatabase(){
		parent::Controller();
		$this->load->helper(array('form', 'url','file','cookie','string')); 
		$this->load->library(array('session', 'validation'));
	}
*/
	public function index(){
		$this->load->library('session');
		$this->load->helper('url');
		$serv = $this->session->userdata('server');
		if(isset($_POST['server']) && isset($_POST['user']) && isset($_POST['pass']) && !empty($_POST['server']) && !empty($_POST['user']) && !empty($_POST['pass'])){
			$dbinfo = array(
							'server' => $_POST['server'], 
							'user' => $_POST['user'], 
							'pass' => $_POST['pass']
							);
			$this->session->set_userdata($dbinfo);
			include_once('ReverseDB.class.php');
			$db = new ReverseDB($_POST['server'], $_POST['user'], $_POST['pass']);
			$data['dbs'] = $db->GetDBs();
			/*** Views ***/
			$this->load->view('header_view');
			$this->load->view('setdatabase2_view',$data);
			$this->load->view('footer_view');
			
		}elseif(isset($serv) && !empty($serv)){
			include_once('ReverseDB.class.php');
			$db = new ReverseDB($this->session->userdata('server'),$this->session->userdata('user'), $this->session->userdata('pass'));
			$data['dbs'] = $db->GetDBs();
			/*** Views ***/
			$this->load->view('header_view');
			$this->load->view('setdatabase2_view',$data);
			$this->load->view('footer_view');
		}else{
			/*** Views ***/
			$this->load->view('header_view');
			$this->load->view('setdatabase_view');
			$this->load->view('footer_view');
		}
	}

}


?>