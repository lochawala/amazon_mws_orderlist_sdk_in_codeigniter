<?php

/**
 * Created by PhpStorm.
 * User: Dreamworld Solutions
 * Date: 10/07/17
 * Time: 10:52 AM
 */
class AdminDashboard extends CI_Controller {

    public $tbl = "of_admin";
    public $controll = "AdminDashboard";

    public function __construct() {
        parent::__construct();
        $this->Validation();
    }

    public function Validation() {
        if ($this->session->userdata('id') == null) {
            redirect(base_url());
        }
    }

    public function index() {
        $data['branch'] = $this->Data_model->Custome_query("select * from of_admin where role='SA' and status='A'");
        $this->load->view('Admin/' . $this->controll, $data);
    }

    public function GetData() {
        $requestData = $_REQUEST;
        $columns = array(
            0 => 'ad.' . $this->tbl . '_id',
            1 => 'ad.name',
            2 => 'ad.update_date',
            3 => 'sws.quantity',
            4 => 'ad.' . $this->tbl . '_id',
        );

        $sql = "SELECT ad.*,sws.quantity as quantity FROM of_admin ad 
LEFT JOIN of_shop_wise_stock sws ON sws.of_admin_id=ad.of_admin_id where ad.role='SA'";
        $query = $this->Data_model->Custome_query($sql);
        $totalData = count($query);
        $totalFiltered = $totalData;

        if (!empty($requestData['search']['value'])) {
            $sql .= " AND ( ad." . $this->tbl . "_id LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR ad.name LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR ad.update_date LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR sws.quantity LIKE '" . $requestData['search']['value'] . "%' )";
//            $sql .= " OR vb.branch_name LIKE '" . $requestData['search']['value'] . "%' )";
        }
        $query = $this->Data_model->Custome_query($sql);
        $totalFiltered = count($query);
        $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "   LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";  // adding length

        $query = $this->Data_model->Custome_query($sql);

        $data = array();
        $cnt = $requestData['start'] + 1;


        foreach ($query as $dt) {
            $nestedData = array();
//            if ($dt['status'] == "A"):
//                $sts = "<a class='status' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='D' ><button class='btn btn-xs btn-success'><span class='fa-stack'><i class='fa fa-flag fa-stack-1x fa-inverse'></i></span></button></a>";
//            else:
//                $sts = "<a class='status' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='A' ><button class='btn btn-xs btn-default'><span class='fa-stack'><i class='fa fa-flag fa-stack-1x fa-inverse'></i></span></button></a>";
//            endif;
////            $edit = "<a class='edit' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='" . $dt['status'] . "' ><button class='btn btn-xs btn-info'><span class='fa-stack'><i class='fa fa-pencil fa-stack-1x fa-inverse'></i></span></button></a>";
//            $delete = "<a class='delete' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='B' ><button class='btn btn-xs btn-danger'><span class='fa-stack'><i class='fa fa-trash-o fa-stack-1x fa-inverse'></i></span></button></a>";
            if ($dt['quantity'] == "") {
                $qty = 0;
            } else {
                $qty = $dt['quantity'];
            }
            $nestedData[] = $cnt++;
            $nestedData[] = $dt['name'];
            $nestedData[] = date('d-m-Y H:i:s', strtotime($dt['update_date']));
            $nestedData[] = $qty;
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
