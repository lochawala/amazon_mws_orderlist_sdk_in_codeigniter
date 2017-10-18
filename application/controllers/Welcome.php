<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public $controll = "Welcome";

    public function __construct() {
        parent::__construct();
        $this->Validation();
    }

    public function Validation() {
        if ($this->session->userdata('id') != null) {
            redirect(base_url() . 'Dashboard');
        }
    }

    public function index() {
        $this->load->view($this->controll);
    }

    public function Process() {
        extract($_POST);
//        echo "<pre>";
//        print_r($_POST);
//        die;
        $admin = $this->Data_model->Process();
        if ($admin != null) {
            $qry = $this->Data_model->Custome_query("select * from of_admin where of_admin_id='" . $_SESSION['id'] . "'");
            if ($qry[0]['role'] == 'A') {
                redirect(base_url() . 'Admin/AdminDashboard');
            } else {
                $con=array('of_admin_id'=>$_SESSION['id']);
                $data=array(
                    'update_date'=>date('Y-m-d H:i:s'),
                );
                $this->Data_model->Update_data('of_admin',$con,$data);
                redirect(base_url() . 'Dashboard');
            }
        } else {
            $sessdata = array('error' => "<strong>Error!</strong> Invalid Username or Password", 'errorcls' => "alert-danger");
            $this->session->set_userdata($sessdata);
            redirect(base_url() . $this->controll);
        }
    }

}
