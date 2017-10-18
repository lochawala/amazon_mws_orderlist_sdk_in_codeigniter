<?php

/**
 * Created by PhpStorm.
 * User: Dreamworld Solutions
 * Date: 10/12/17
 * Time: 10:25 AM
 */
//require_once('.config.inc.php');

class Orders extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        require(APPPATH . 'libraries/amazon_order_api/src/MarketplaceWebServiceOrders/Samples/.config.inc.php');
        require(APPPATH . 'libraries/amazon_order_api/src/MarketplaceWebServiceOrders/Client.php');
        require(APPPATH . 'libraries/amazon_order_api/src/MarketplaceWebServiceOrders/Model/ListOrdersRequest.php');
    }

    function index()
    {
//        echo base_url();
//        die;
        $serviceUrl = "https://mws.amazonservices.in/Orders/2013-09-01";
        $config = array(
            'ServiceURL' => $serviceUrl,
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 3,
        );

        $service = new MarketplaceWebServiceOrders_Client(
            AWS_ACCESS_KEY_ID,
            AWS_SECRET_ACCESS_KEY,
            APPLICATION_NAME,
            APPLICATION_VERSION,
            $config);


        $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
        $request->setSellerId('AGNFZGZRZBUP1');
        $request->setMarketplaceId('A21TJRUUN4KGV');
        $request->setCreatedAfter("2017-01-01T18:12:21");

        // object or array of parameters
        $this->invokeListOrders($service, $request);
    }


    function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request)
    {
        try {
            $response = $service->ListOrders($request);
            $dom = new DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $xml = simplexml_load_string($dom->saveXML());
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            echo "<pre>";
            print_r($array['ListOrdersResult']['Orders']['Order']);



            //      echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
        } catch (MarketplaceWebServiceOrders_Exception $ex) {
            echo "<pre>";
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
        }
    }


    public function getOrders_old()
    {

//        echo "<pre>";
        $param = array();
        $param['AWSAccessKeyId'] = 'AKIAJGRT6ZDPD572XQ7A';
        $param['Action'] = 'ListOrders';
        $param['MWSAuthToken'] = 'amzn.mws.e0af6e6a-f6a0-e436-27ea-85cb1197e4a8';
        $param['MarketplaceId'] = 'A21TJRUUN4KGV';
        $param['FulfillmentChannel.Channel.1'] = 'MFN';
        $param['PaymentMethod.Method.1'] = 'COD';
        $param['OrderStatus.Status.1'] = 'Pending';
        $param['OrderStatus.Status.2'] = 'PendingAvailability';
        $param['SellerId'] = 'AGNFZGZRZBUP1';
        $param['SignatureMethod'] = 'HmacSHA256';
        $param['SignatureVersion'] = '2';
        $param['CreatedAfter'] = "2017-09-01T13:41:49Z";
        $param['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
        $param['Version'] = '2013-09-01';
        $secret = 'Ebr1UOM3ZAXptQ0I4YfBQ/3bInn4h2HcPJe/y2on';

        $url = array();
        foreach ($param as $key => $val) {

            $val = str_replace("%7E", "~", rawurlencode($val));
            $url[] = $key . "=" . $val;
        }

        sort($url);

        $arr = implode('&', $url);

        $sign = 'GET' . "\n";
        $sign .= 'mws.amazonservices.in' . "\n";
        $sign .= '/Orders/2013-09-01' . "\n";
        $sign .= $arr;


        $signature = hash_hmac("sha256", $sign, $secret, true);
        $signature = urlencode(base64_encode($signature));

        $link = "https://mws.amazonservices.in/Orders/2013-09-01?";
        $link .= $arr;
        $link .= "&Signature=" . $signature;
        echo($link); //for debugging - you can paste this into a browser and see if it loads.


        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        echo "<pre>";
        print_r($response);
        print_r($info);

    }

    public function getProduct()
    {
        $param = array();
        $param['AWSAccessKeyId'] = 'AKIAJGRT6ZDPD572XQ7A';
        $param['Action'] = 'GetLowestOfferListingsForASIN';
        $param['MarketplaceId'] = 'A21TJRUUN4KGV';

        $param['SellerId'] = 'AGNFZGZRZBUP1';
        $param['SignatureMethod'] = 'HmacSHA256';
        $param['SignatureVersion'] = '2';
        $param['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
        $param['Version'] = '2011-10-01';
        $param['ItemCondition'] = 'new';
        $param['ASINList.ASIN.1'] = 'B00C5XBAOA';
        $secret = 'Ebr1UOM3ZAXptQ0I4YfBQ/3bInn4h2HcPJe/y2on';

        $url = array();
        foreach ($param as $key => $val) {

            $key = str_replace("%7E", "~", rawurlencode($key));
            $val = str_replace("%7E", "~", rawurlencode($val));
            $url[] = "{$key}={$val}";
        }

        sort($url);

        $arr = implode('&', $url);

        $sign = 'GET' . "\n";
        $sign .= 'mws.amazonservices.in' . "\n";
        $sign .= '/Products/2011-10-01' . "\n";
        $sign .= $arr;

        $signature = hash_hmac("sha256", $sign, $secret, true);
        $signature = urlencode(base64_encode($signature));

        $link = "https://mws.amazonservices.in/Products/2011-10-01?";
        $link .= $arr . "&Signature=" . $signature;
        echo($link); //for debugging - you can paste this into a browser and see if it loads.

        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);


        echo "<pre>";
        $json = json_encode($response);
        $array = json_decode($json, TRUE);
        print_r($array);
        print_r($info);
    }

    public function flipkart_orders()
    {
        $url = "https://sandbox-api.flipkart.net/sellers/v2/orders/search";
        $curl = curl_init();
        $searchData = '{
          "filter": {
            "orderDate": {
              "fromDate": "2015-11-05T08:15:30Z",
              "toDate": "2015-12-05T08:15:30Z"
            }
          }
        }';
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $searchData);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//try to make it as true. making ssl verifyer as false will lead to secruty issues
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization:Bearer ' . $tokan['access_token'],
            ''
        ));
        $response = curl_exec($curl);
        echo $response;
    }


    public function flipkart()
    {
        $url = 'https://api.flipkart.net/oauth-service/oauth/token?grant_type=client_credentials&scope=Seller_Api';
        $curl = curl_init();
        $headers = array(
            'appid:8385914950540817ba8588007a539b8a01a6',
            'app-secret:2e9cc2c0d3eddb8b7dc9777e6d51861f5'
        );

        $json_data = json_encode($headers);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/octet-stream',
            'Content-Length:' . strlen($json_data),
        ));

        $result = curl_exec($curl);
        $ee = curl_getinfo($curl);
        echo "<pre>";
        print_r($ee);
        curl_close($curl);

        print_r($result);
    }

}