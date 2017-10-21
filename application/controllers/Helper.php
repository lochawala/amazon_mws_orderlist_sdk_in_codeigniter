<?php


class Helper extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function change_status()
    {
        extract($_REQUEST);
        $con = array($tbl . '_id' => $id);
        $data = array('status' => $status);
        $this->Data_model->Update_data($tbl, $con, $data);
        echo 1;
    }

    public function GetEditData()
    {
        extract($_POST);
        $con = array($tbl . '_id' => $id);
        $data = $this->Data_model->Get_data_one($tbl, $con);
        echo json_encode($data);
    }

    public function UpdateData()
    {
        extract($_REQUEST);
        $con = array($tbl . '_id' => $hid);
        $data = array(
            'name' => $catname,
            'update_date' => date('Y-m-d H:S:i')
        );
        $this->Data_model->Update_data($tbl, $con, $data);
        $sessdata = array('error' => '<strong>Success!</strong> Update Successfully', 'errorcls' => 'alert-success');
        $this->session->set_userdata($sessdata);
        redirect(base_url() . $this->config->item('admin') . $controller);
    }

    public function Logout()
    {
        session_destroy();
        redirect(base_url());
    }

}
