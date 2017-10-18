<?php

class ShopItem extends CI_Controller {

    public $tbl = "of_items";
    public $controll = "ShopItem";

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
        $data['item_type'] = $this->Data_model->Custome_query("select * from of_item_type where status !='B'");
        $data['unit_type'] = $this->Data_model->Custome_query("select * from of_unit_type where status !='B'");
        $data['packing'] = $this->Data_model->Custome_query("select * from of_packing where status !='B'");
        $data['gst_type'] = $this->Data_model->Custome_query("select * from of_gst_type where status !='B'");
        $this->load->view('Admin/'.$this->controll, $data);
    }

    public function addData() {
        extract($_REQUEST);
        $data = array(
            'item_code' => $itemcode,
            'item_name' => $itemname,
            'print_name' => $printname,
            'of_item_type_id' => $itemtype,
            'of_unit_type_id' => $unit,
            'of_packing_type_id' => $packing_type,
            'hsn_code' => $hsn,
            'discount' => $discount,
            'purchase_rate' => $purchase_rate,
            'purchase_rate_include_gst' => isset($purchase_rate_chk) ? 'Y' : 'N',
            'sales_rate' => $sale_rate,
            'sales_rate_include_gst' => isset($sale_rate_chk) ? 'Y' : 'N',
            'mrp' => $mrp,
            'sale_gst_class' => $sale_gst_class,
            'check_negative_stock' => isset($sale_gst_class_chk) ? 'Y' : 'N',
            'pcs_per_pack' => $pcs_per_pack,
            'avrg_wight' => $avg_wt,
            'remark' => $remark,
            'gross_profit' => $gross_profit,
            'create_date' => date('Y-m-d H:i:s'),
        );
//        echo "<pre>";
//        print_r($data);die;
        $id = $this->Data_model->Insert_data_id($this->tbl, $data);
        $sessdata = array('error' => "<strong>Success!</strong> Add New Recored", 'errorcls' => "alert-success");
        $this->session->set_userdata($sessdata);
        redirect(base_url() . 'Admin/' . $this->controll . '');
    }

    public function GetData() {
        $requestData = $_REQUEST;
        $columns = array(
            0 => 'oit.of_items_id',
            1 => 'oit.item_code',
            2 => 'oit.item_name',
            3 => 'oit.print_name',
            4 => 'ityp.of_item_type_id',
            5 => 'oun.of_unit_type_id',
            6 => 'opc.of_packing_type_id',
            7 => 'oit.hsn_code',
            8 => 'oit.discount',
            9 => 'oit.purchase_rate',
            10 => 'oit.purchase_rate_include_gst',
            11 => 'oit.sales_rate',
            12 => 'oit.sales_rate_include_gst',
            13 => 'oit.mrp',
            14 => 'ogst.sale_gst_class',
            15 => 'oit.check_negative_stock',
            16 => 'oit.pcs_per_pack',
            17 => 'oit.avrg_wight',
            18 => 'oit.remark',
            19 => 'oit.gross_profit',
            20 => 'oit.of_items_id',
        );

        $sql = "SELECT oit.*,ityp.name as itemname,oun.name as unitname,opc.name as packingname,ogst.name as gstclass FROM of_items oit
LEFT JOIN of_item_type ityp ON oit.of_item_type_id=ityp.of_item_type_id
LEFT JOIN of_unit_type oun ON oun.of_unit_type_id=oit.of_unit_type_id
LEFT JOIN of_packing opc ON opc.of_packing_id=oit.of_packing_type_id
LEFT JOIN of_gst_type ogst ON ogst.of_gst_type_id=oit.sale_gst_class where oit.status !='B'";
        $query = $this->Data_model->Custome_query($sql);
        $totalData = count($query);
        $totalFiltered = $totalData;

        if (!empty($requestData['search']['value'])) {
            $sql .= " AND ( oit.of_items_id LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.item_code LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.item_name LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.print_name LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR ityp.of_item_type_id LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oun.of_unit_type_id LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR opc.of_packing_type_id LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.hsn_code LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.discount LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.purchase_rate LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.purchase_rate_include_gst LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.sales_rate LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.sales_rate_include_gst LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.mrp LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR ogst.sale_gst_class LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.check_negative_stock LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.pcs_per_pack LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.remark LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR oit.gross_profit LIKE '" . $requestData['search']['value'] . "%') ";
           
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
            if($dt['purchase_rate_include_gst']=='Y')
            {
                $perrate='Include Gst';
            }else
            {
                $perrate='Not Include Gst';
            }
            if($dt['sales_rate_include_gst']=='Y')
            {
                $salesrate='Include Gst';
            }else
            {
                $salesrate='Not Include Gst';
            }
            if($dt['check_negative_stock']=='Y')
            {
                $ngstock='YES';
            }else
            {
                $ngstock='NO';
            }
            $nestedData[] = $cnt++;
            $nestedData[] = $dt['item_code'];
            $nestedData[] = $dt['item_name'];
            $nestedData[] = $dt['print_name'];
            $nestedData[] = $dt['itemname'];
            $nestedData[] = $dt['unitname'];
            $nestedData[] = $dt['packingname'];
            $nestedData[] = $dt['hsn_code'];
            $nestedData[] = $dt['discount'];
            $nestedData[] = $dt['purchase_rate'];
            $nestedData[] = $perrate;
            $nestedData[] = $dt['sales_rate'];
            $nestedData[] = $salesrate;
            $nestedData[] = $dt['mrp'];
            $nestedData[] = $dt['gstclass'].'%';
            $nestedData[] = $ngstock;
            $nestedData[] = $dt['pcs_per_pack'];
            $nestedData[] = $dt['avrg_wight'];
            $nestedData[] = $dt['remark'];
            $nestedData[] = $dt['gross_profit'];
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
