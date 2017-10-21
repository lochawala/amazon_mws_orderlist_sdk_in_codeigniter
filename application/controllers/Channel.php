<?php


class Channel extends CI_Controller
{


    public $controll = "Channel";
    public $tbl = "of_marketplace_credential";


    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['marketplace'] = $this->Data_model->custome_query('select om.*,(if((SELECT of_marketplace_id FROM of_marketplace_credential WHERE of_marketplace_id=om.of_marketplace_id) IS NULL ,"NO","YES")) is_marketplace from of_marketplace om WHERE om.status="A"');
//        echo "<pre>";
//        print_r($data);
        $this->load->view('Channel', $data);
    }

    public function addChannel($id)
    {
//        $ids = preg_replace("/[^ \w]+/", "", $id);
        $ids = preg_replace('/[^a-zA-Z0-9-.]/', '', $id);

        $data['marketplace'] = $this->Data_model->custome_query('select om.*,(if((SELECT of_marketplace_id FROM of_marketplace_credential WHERE of_marketplace_id=om.of_marketplace_id) IS NULL ,"NO","YES")) is_marketplace from of_marketplace om WHERE om.status="A" and replace(LOWER(om.name)," ","-")="' . $ids . '"');


        $data['marketplace_field'] = $this->Data_model->custome_query('select * from of_marketplace_wise_field WHERE of_marketplace_id = ' . $data['marketplace'][0]['of_marketplace_id']);
        $data['marketplace_detail'] = $this->Data_model->custome_query('select * from of_marketplace_credential WHERE of_marketplace_id = ' . $data['marketplace'][0]['of_marketplace_id'] . ' and of_admin_id=' . $_SESSION['id']);
//        echo "<pre>";
//        print_r($data);
//        die;
        $this->load->view('addChannel', $data);
    }

    public function addData()
    {
        extract($_REQUEST);
        $qry = $this->Data_model->custome_query('select * from of_marketplace_credential WHERE of_marketplace_id = ' . $hid . ' and of_admin_id=' . $_SESSION['id']);
        if (count($qry) > 0):
            $con = [
                $this->tbl . '_id' => $qry[0]['of_marketplace_credential_id']
            ];

            isset($seller_id) ? $data['seller_id'] = $seller_id : '';
            isset($marketplace_id) ? $data['marketplace_id'] = $marketplace_id : '';
            isset($aws_access_key_id) ? $data['aws_access_key_id'] = $aws_access_key_id : '';
            isset($secret_key) ? $data['secret_key'] = $secret_key : '';
            isset($username_of_panel) ? $data['username_of_panel'] = $username_of_panel : '';
            isset($password_of_panel) ? $data['password_of_panel'] = $password_of_panel : '';
            isset($username) ? $data['username'] = $username : '';
            isset($password) ? $data['password'] = $password : '';
            isset($flipkart_application_id) ? $data['flipkart_application_id'] = $flipkart_application_id : '';
            isset($flipkart_application_secret) ? $data['flipkart_application_secret'] = $flipkart_application_secret : '';
            isset($merchant_id) ? $data['merchant_id'] = $merchant_id : '';
            isset($api_token) ? $data['api_token'] = $api_token : '';
            $data['update_date'] = date('Y-m-d H:i:s');


//            array_filter($data);
//            echo "<pre>";
//            print_r($data);
//            die;
            $id = $this->Data_model->Update_data($this->tbl, $con, $data);
        else:
            $data = [
                'of_admin_id' => isset($_SESSION['id']) ? $_SESSION['id'] : "",
                'of_marketplace_id' => isset($hid) ? $hid : "",
                'seller_id' => isset($seller_id) ? $seller_id : "",
                'marketplace_id' => isset($marketplace_id) ? $marketplace_id : "",
                'aws_access_key_id' => isset($aws_access_key_id) ? $aws_access_key_id : "",
                'secret_key' => isset($secret_key) ? $secret_key : "",
                'username_of_panel' => isset($username_of_panel) ? $username_of_panel : "",
                'password_of_panel' => isset($password_of_panel) ? $password_of_panel : "",
                'username' => isset($username) ? $username : "",
                'password' => isset($password) ? $password : "",
                'flipkart_application_id' => isset($flipkart_application_id) ? $flipkart_application_id : "",
                'flipkart_application_secret' => isset($flipkart_application_secret) ? $flipkart_application_secret : "",
                'merchant_id' => isset($merchant_id) ? $merchant_id : "",
                'api_token' => isset($api_token) ? $api_token : "",
                'create_date' => date('Y-m-d H:i:s'),
            ];
            $id = $this->Data_model->Insert_data_id($this->tbl, $data);
        endif;
        $sessdata = array('error' => "<strong>Success!</strong> Add Record", 'errorcls' => "alert-success");
        $this->session->set_userdata($sessdata);
        redirect(base_url() . $this->controll . '/addChannel/' . $url);
    }
}
