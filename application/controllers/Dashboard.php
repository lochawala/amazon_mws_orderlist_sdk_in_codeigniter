<?php


class Dashboard extends CI_Controller
{

    public $controll = "Dashboard";
    public $tbl = "Dashboard";

    public function __construct()
    {
        parent::__construct();
        $this->Validation();
    }

    public function Validation()
    {
        if ($this->session->userdata('id') == null) {
            redirect(base_url());
        }
    }

    public function index()
    {
        $this->load->view($this->controll);
    }

    public function addData()
    {
        extract($_REQUEST);

        $data = array(
            'branch_name'=>$branch_name,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'timing' => $timing,
            'phone_no' => $phone,
            'create_date' => date('Y-m-d H:i:s')
        );
        $id = $this->Data_model->Insert_data_id($this->tbl, $data);
        $sessdata = array('error' => "<strong>Success!</strong> Add New Brand", 'errorcls' => "alert-success");
        $this->session->set_userdata($sessdata);
        redirect(base_url(). $this->controll . '');
    }


    public function GetData()
    {
        $requestData = $_REQUEST;
        $columns = array(
            0 => 'va.' . $this->tbl . '_id',
            1 => 'va.name',
            2 => 'va.phone_no',
            3 => 'va.email',
            4 => 'va.appointment_date',
            5 => 'vb.vs_branch_id',
            6 => 'va.message',
            7 => 'va.' . $this->tbl . '_id',
        );

        $sql = "select * from " . $this->tbl . " va LEFT JOIN vs_branch vb ON va.vs_branch_id=vb.vs_branch_id  where va.status!='B'";
        $query = $this->Data_model->Custome_query($sql);
        $totalData = count($query);
        $totalFiltered = $totalData;

        if (!empty($requestData['search']['value'])) {
            $sql .= " AND ( " . $this->tbl . "_id LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR va.name LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR va.phone_no LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR va.email LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR vb.branch_name LIKE '" . $requestData['search']['value'] . "%' )";
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
            $nestedData[] = $cnt++;
            $nestedData[] = $dt['name'];
            $nestedData[] = $dt['phone_no'];
            $nestedData[] = $dt['email'];
            $nestedData[] = $dt['appointment_date'];
            $nestedData[] = $dt['branch_name'];
            $nestedData[] = $dt['message'];
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
