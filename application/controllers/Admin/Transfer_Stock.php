<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer_Stock extends CI_Controller {

    public $tbl = "of_transfer";
    public $controll = "Transfer_Stock";

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
        $data['transfer'] = $this->Data_model->Custome_query("select * from of_transfer where status !='B' order by of_transfer_id desc limit 1");
        $data['items'] = $this->Data_model->Custome_query("select * from of_items where status !='B'");
        $data['shop'] = $this->Data_model->Custome_query("select * from of_admin where status !='B' and role='SA'");
        $this->load->view('Admin/' . $this->controll, $data);
    }

    public function addData() {
        extract($_REQUEST);

        $data = array(
            'transfer_no' => $invoice_no,
            'of_from_admin_id' => $from_shop,
            'of_to_admin_id' => $to_shop,
            'transfer_date' => date('Y-m-d',  strtotime($fdate)),
            'remark' => $remk,
            'total_item' => count($itemtype),
            'total_pcs' => $finalpcs,
            'total_qty' => $finalqty,
            'total_amount' => $finalamt,
        );

        $id = $this->Data_model->Insert_data_id('of_transfer', $data);
        $bill_item = [];
        $item = 0;
        for ($i = 0; $i < count($itemtype); $i++) {
            $bill_item[] = array(
                'of_transfer_id' => $id,
                'of_items_id' => $itemtype[$i],
                'quantity' => $qty[$i],
                'create_date' => date('Y-m-d H:i:s'),
            );
            $sql = $this->Data_model->Custome_query("select * from of_shop_wise_stock where of_items_id='" . $itemtype[$i] . "' and of_admin_id='" . $_SESSION['id'] . "'");
            if (count($sql) > 0) {
                $totqty = 0;
                if ($sql[0]['of_items_id'] == $itemtype[$i]) {
                    $oldqty = $sql[0]['quantity'];
                    $totqty = $oldqty + $qty[$i];
                    $con = array('of_shop_wise_stock_id' => $sql[0]['of_shop_wise_stock_id']);
                    $data = array('quantity' => $totqty);
                    $this->Data_model->Update_data('of_shop_wise_stock', $con, $data);
                }
            } else {
                $bill_items = array(
                    'of_items_id' => $itemtype[$i],
                    'of_admin_id' => $_SESSION['id'],
                    'quantity' => $qty[$i],
                    'create_date' => date('Y-m-d H:i:s'),
                );
                $this->Data_model->Insert_data_id('of_shop_wise_stock', $bill_items);
            }
        }
        $this->Data_model->Insert_batch('of_transfer_items', $bill_item);
        $sessdata = ['error' => '<strong>Success!</strong> Add Recored.', 'errorcls' => 'alert-success'];
        $this->session->set_userdata($sessdata);
        redirect(base_url() . 'Admin/' . $this->controll . '');
    }

    public function GetData() {
        $requestData = $_REQUEST;
        $columns = array(
            0 => 'otf.of_transfer_id',
            1 => 'ad.from_shop',
            2 => 'ad.to_shop',
            3 => 'otf.total_item',
            4 => 'otf.total_pcs',
            5 => 'otf.total_qty',
            6 => 'otf.total_amount',
            7 => 'otf.transfer_date',
           
        );

        $sql = "SELECT *,( SELECT ad.name FROM of_admin ad WHERE ad.of_admin_id=otf.of_from_admin_id) from_shop ,
( SELECT ad.name FROM of_admin ad WHERE ad.of_admin_id=otf.of_to_admin_id) to_shop
FROM of_transfer otf where otf.status !='B'";
        $query = $this->Data_model->Custome_query($sql);
        $totalData = count($query);
        $totalFiltered = $totalData;

        if (!empty($requestData['search']['value'])) {
            $sql .= " AND ( otf.of_transfer_id LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR ad.from_shop LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR ad.to_shop LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR otf.total_item LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR otf.total_pcs LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR otf.total_qty LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR otf.total_amount LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR otf.transfer_date LIKE '" . $requestData['search']['value'] . "%' )";
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
//            $edit = "<a class='edit' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='" . $dt['status'] . "' ><button class='btn btn-xs btn-info'><span class='fa-stack'><i class='fa fa-pencil fa-stack-1x fa-inverse'></i></span></button></a>";
//            $delete = "<a class='delete' data-id='" . $dt[$this->tbl . '_id'] . "' data-status='B' ><button class='btn btn-xs btn-danger'><span class='fa-stack'><i class='fa fa-trash-o fa-stack-1x fa-inverse'></i></span></button></a>";
            $nestedData[] = $cnt++;
            $nestedData[] = $dt['from_shop'];
            $nestedData[] = $dt['to_shop'];
            $nestedData[] = $dt['total_item'];
            $nestedData[] = $dt['total_pcs'];
            $nestedData[] = $dt['total_qty'];
            $nestedData[] = $dt['total_amount'];
            $nestedData[] = date('d-m-Y',  strtotime($dt['transfer_date']));
//            $nestedData[] = $sts . "&nbsp" . $edit . "&nbsp" . $delete;
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

    public function GetItem() {
        extract($_REQUEST);
        $sql = $this->Data_model->Custome_query("SELECT oit.*,ityp.name as itemname,oun.name as unitname,opc.name as packingname,ogst.name as gstclass FROM of_items oit
LEFT JOIN of_item_type ityp ON oit.of_item_type_id=ityp.of_item_type_id
LEFT JOIN of_unit_type oun ON oun.of_unit_type_id=oit.of_unit_type_id
LEFT JOIN of_packing opc ON opc.of_packing_id=oit.of_packing_type_id
LEFT JOIN of_gst_type ogst ON ogst.of_gst_type_id=oit.sale_gst_class where oit.status !='B' and oit.of_items_id ='" . $id . "'");
        echo json_encode($sql);
    }

    public function Getshop() {
        extract($_REQUEST);
        $sql = $this->Data_model->Custome_query("select * from of_admin where of_admin_id !='" . $shopid . "' and role='SA'");
        $opt = "";
        foreach ($sql as $dt) {
            $opt.="<option value='" . $dt['of_admin_id'] . "'>" . $dt['name'] . "</option>";
        }
        echo $opt;
    }

    public function Dynamicdata() {
        extract($_REQUEST);
        $sql = $this->Data_model->Custome_query("SELECT oit.*,ityp.name as itemname,oun.name as unitname,opc.name as packingname,ogst.name as gstclass FROM of_items oit
LEFT JOIN of_item_type ityp ON oit.of_item_type_id=ityp.of_item_type_id
LEFT JOIN of_unit_type oun ON oun.of_unit_type_id=oit.of_unit_type_id
LEFT JOIN of_packing opc ON opc.of_packing_id=oit.of_packing_type_id
LEFT JOIN of_gst_type ogst ON ogst.of_gst_type_id=oit.sale_gst_class where oit.status !='B' and oit.of_items_id not in (" . trim($pid, ',') . ")");
        echo json_encode($sql);
    }

}
