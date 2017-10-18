<?php

/**
 * Created by PhpStorm.
 * User: Dreamworld Solutions
 * Date: 10/07/17
 * Time: 10:52 AM
 */
class Create_Account extends CI_Controller {

    public $tbl = "of_admin";
    public $controll = "Create_Account";

    public function __construct() {
        parent::__construct();
        $this->Validation();
    }

    public function Validation() {
        if ($this->session->userdata('id') == null) {
            redirect(base_url() . 'Admin');
        }
    }

    public function index() {
        $this->load->view('Admin/' . $this->controll);
    }

    public function addData() {
        extract($_REQUEST);
        $data = array(
            'name' => $adminname,
            'username' => $email,
            'password' => $pwd,
            'role' => $type,
            'create_date' => date('Y-m-d H:i:s'),
            'status' => $status
        );
        $id = $this->Data_model->Insert_data_id($this->tbl, $data);
        $sessdata = array('error' => "<strong>Success!</strong> Add New Recored", 'errorcls' => "alert-success");
        $this->session->set_userdata($sessdata);
        redirect(base_url() . 'Admin/' . $this->controll . '');
    }

    public function GetData() {
        $requestData = $_REQUEST;
        $columns = array(
            0 => $this->tbl . '_id',
            1 => 'name',
            2 => 'username',
            3 => 'role',
            4 => 'status',
            5 => $this->tbl . '_id',
        );

        $sql = "select * from " . $this->tbl . " where  status!='B' and role='SA'";
        $query = $this->Data_model->Custome_query($sql);
        $totalData = count($query);
        $totalFiltered = $totalData;

        if (!empty($requestData['search']['value'])) {
            $sql .= " AND ( " . $this->tbl . "_id LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR name LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR username LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR role LIKE '" . $requestData['search']['value'] . "%' )";
           
        }
        $query = $this->Data_model->Custome_query($sql);
        $totalFiltered = count($query);
        $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "   LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";  // adding length

        $query = $this->Data_model->Custome_query($sql);

        $data = array();
        $cnt = $requestData['start'] + 1;


        foreach ($query as $dt) {
            $nestedData = array();
            if ($dt['status'] == "A"):
                $sts = "<a class='status' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='D' ><button class='btn btn-xs btn-success'><span class='fa-stack'><i class='fa fa-flag fa-stack-1x fa-inverse'></i></span></button></a>";
            else:
                $sts = "<a class='status' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='A' ><button class='btn btn-xs btn-default'><span class='fa-stack'><i class='fa fa-flag fa-stack-1x fa-inverse'></i></span></button></a>";
            endif;
//            $edit = "<a class='edit' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='" . $dt['status'] . "' ><button class='btn btn-xs btn-info'><span class='fa-stack'><i class='fa fa-pencil fa-stack-1x fa-inverse'></i></span></button></a>";
            $delete = "<a class='delete' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='B' ><button class='btn btn-xs btn-danger'><span class='fa-stack'><i class='fa fa-trash-o fa-stack-1x fa-inverse'></i></span></button></a>";
            if ($dt['role'] == 'SA') {
                $sadmin = 'SubAdmin';
            } else {
                $sadmin = 'Admin';
            }

            $nestedData[] = $cnt++;
            $nestedData[] = $dt['name'];
            $nestedData[] = $dt['username'];
            $nestedData[] = $sadmin;
            $nestedData[] = $sts . "&nbsp" . $delete;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

}
