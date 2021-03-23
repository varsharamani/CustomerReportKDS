<?php

namespace App\Http\Controllers;
session_start();
use DB;
use File;
use Session;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Auth;
class GetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
      $this->username='a193e7a4cc8b59ed17f948458f94f824';
      $this->password = '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';
      //ini_set('display_errors',1);
      //$this->load->library('session');
      //error_reporting(E_ALL);
    }

    public static function __curl($url,$username,$password){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);  
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: POST') );
       // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
        $responseArr = json_decode($response);
        return $responseArr;
    }

   

    // get Product List

    public function index(){
        $username='a193e7a4cc8b59ed17f948458f94f824';
        $password = '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';
        $baseUrl = 'https://lord-jameson-dog.myshopify.com/admin/api/2021-01/customers/count.json';
        $items_per_page = 250;
        $next_page = '';
        $last_page = false;
                 
        $countRefund  = self::__curl($baseUrl,$username,$password);
        $totalRefund = $countRefund->count;
        $pageRefund = ceil($totalRefund/$items_per_page);
        //echo $pageRefund;die;
        $k=0;$customerData = array();
              while(!$last_page) {
                  $responseData = array();
                    $url = 'https://a193e7a4cc8b59ed17f948458f94f824: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@lord-jameson-dog.myshopify.com/admin/api/2021-01/customers.json?limit=' . $items_per_page . $next_page; 
                 
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$headers) {
                        $len = strlen($header);
                        $header = explode(':', $header, 2);
                        if (count($header) >= 2) {
                            $headers[strtolower(trim($header[0]))] = trim($header[1]);
                        }
                        return $len;
                    });
                    $result = curl_exec($curl);
                    if(isset($headers['link'])) {
                        $links = explode(',', $headers['link']);
                        foreach($links as $link) {
                            if(strpos($link, 'rel="next"')) {
                                preg_match('~<(.*?)>~', $link, $next);
                                $url_components = parse_url($next[1]);
                                parse_str($url_components['query'], $params);
                                //echo $params['page_info'];die;
                             $next_page = '&page_info=' . $params['page_info'];
                            }
                        }
                    } else {
                        $last_page = true; // if missing "link" parameter - there's only one page of results = last_page
                    }
                   
                    if($pageRefund==$k){
                        $last_page = true;
                        break;
                    }
                    $k++;
                    $responseData = json_decode($result);
                    //echo "<PRE>";print_r($responseData);die;
                    if($responseData->customers){
                       for($l = 0;$l<count($responseData->customers);$l++){
                          if(isset($responseData->customers[$l]->id)){
                              array_push($customerData,$responseData->customers[$l]);
                          }   
                      }
                    }      
              }
             //  echo count($customerData);die;
              for($i=0;$i<count($customerData);$i++){
                $orderData=$responseArr = array();
                $lastorderData = array();
                $last_order = '';
                if(!empty($customerData[$i]->last_order_id)){ 
                  $baseUrl = 'https://lord-jameson-dog.myshopify.com/admin/api/2021-01/orders/'.$customerData[$i]->last_order_id.'.json';
                    $lastorderData = self::__curl($baseUrl,$this->username,$this->password);
                }

                if(!empty($lastorderData)){
                 // print_r($lastorderData);die;
                    $last_order = $lastorderData->order->created_at;
                }
                if(isset($customerData[$i]->addresses)){
                  $address = array();
                  $address = $customerData[$i]->addresses;
                  $address1=$city=$country=$zip=$add='';
                  if(isset($address[0]->address1)){
                    $add.= $address[0]->address1.',';
                  }
                  if(isset($address[0]->city)){
                    $add.= $address[0]->city.',';
                  }
                  if(isset($address[0]->country)){
                    $add.= $address[0]->country.',';
                  }
                  if(isset($address[0]->zip)){
                    $add.= $address[0]->zip;
                  }
                  //$add = $address1.','.$city.','.$country.','.$zip;
                }
                $avgAmount = '';
                if(!empty($customerData[$i]->orders_count)){
                  if(!empty($customerData[$i]->orders_count)){
                    $avgAmount = $customerData[$i]->total_spent/$customerData[$i]->orders_count;
                  }
                }
                if($avgAmount == '')
                {
                  $avgAmount = 0;
                }
                $customer = DB::select('select * from tbl_customer where customer_id ='.$customerData[$i]->id);

                  if(count($customer) == 0){
                      $values = array('customer_id' => $customerData[$i]->id,'first_name' => $customerData[$i]->first_name,'last_name'=>$customerData[$i]->last_name,'address'=>$add,'mobileno'=>$customerData[$i]->phone,'email'=>$customerData[$i]->email,'customer_created_at'=>$customerData[$i]->created_at,'average_amount'=>round($avgAmount,'2'),'total_order'=>$customerData[$i]->orders_count,'last_order_date'=>$last_order);
                      DB::table('tbl_customer')->insert($values);
                  }else{
                    $updateArr = array('first_name' => $customerData[$i]->first_name,'last_name'=>$customerData[$i]->last_name,'address'=>$add,'mobileno'=>$customerData[$i]->phone,'email'=>$customerData[$i]->email,'average_amount'=>round($avgAmount,'2'),'total_order'=>$customerData[$i]->orders_count,'last_order_date'=>$last_order);
                       DB::table('tbl_customer')->where('customer_id', $customerData[$i]->id)->update($updateArr);
                  }   
              }
    }

    public function getCustomer(){
       // print_r($_POST);die;
        $customer = DB::select('select * from tbl_customer where total_order >= 1 ');
        if(!empty($customer)){
          $_SESSION['customer'] = $customer;
        }
        $mindate = DB::select('SELECT min(date(customer_created_at)) as mindate from tbl_customer');
        $maxdate = DB::select('SELECT max(date(customer_created_at)) as maxdate from tbl_customer');
        //print_r($mindate);die();
        $finalDate = date("m/d/Y", strtotime($mindate[0]->mindate)).' - '.date("m/d/Y", strtotime($maxdate[0]->maxdate));
       // echo $finalDate;die;
        // $finalDate = $mindate[0]->mindate.' - '.$maxdate[0]->maxdate;
        echo view ('customer_report',['customerData' => $customer,'date'=>$finalDate]);
    }

     public function getCustomerFilter(){
      $dateAtt = array();
        $dateAtt = explode(" - ",$_POST['daterange']);
        //echo 'select * from tbl_customer where total_order >= 1 and date(customer_created_at) >= "'.date('Y-m-d', strtotime($dateAtt[0])).'" and date(customer_created_at) <= "'.date('Y-m-d', strtotime($dateAtt[1])).'"';die;
        $customer = DB::select('select * from tbl_customer where total_order >= 1 and date(customer_created_at) >= "'.date('Y-m-d', strtotime($dateAtt[0])).'" and date(customer_created_at) <= "'.date('Y-m-d', strtotime($dateAtt[1])).'"');
        if(!empty($customer)){
          $_SESSION['customer'] = $customer;
        }
        echo view ('customer_report',['customerData' => $customer,'date'=>$_POST['daterange']]);
    }

     public function export(Request $request,$type) {
      $customerData = array();
      if(!empty($_SESSION['customer'])){
          $customerData = $_SESSION['customer'];
      }
      // $customerData = DB::select('select * from tbl_customer');
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setCellValue('A1', 'Customer Name');
      $sheet->setCellValue('B1', 'Address');
      $sheet->setCellValue('C1', 'Phone');
      $sheet->setCellValue('D1', 'Email');
      $sheet->setCellValue('E1', 'Date they became a customer');
      $sheet->setCellValue('F1', 'Average Order Amount');
      $sheet->setCellValue('G1', 'Total Number of Orders');
      $rows = 2;
      foreach($customerData as $customer){
      $sheet->setCellValue('A' . $rows, $customer->first_name.' '.$customer->last_name);
      $sheet->setCellValue('B' . $rows, $customer->address);
      $sheet->setCellValue('C' . $rows, $customer->mobileno);
      $sheet->setCellValue('D' . $rows, $customer->email);
      $sheet->setCellValue('E' . $rows, date("m/d/Y", strtotime($customer->customer_created_at)));
      $sheet->setCellValue('F' . $rows, '$'.round($customer->average_amount,'2'));
      $sheet->setCellValue('G' . $rows, $customer->total_order);
      $rows++;
      }
      $fileName = "Customer_report.".$type;
      if($type == 'xlsx') {
      $writer = new Xlsx($spreadsheet);
      } else if($type == 'xls') {
      $writer = new Xls($spreadsheet);
      }
      $writer->save("export/".$fileName);
      header("Content-Type: application/vnd.ms-excel");
      return redirect(url('/')."/export/".$fileName);
    }
}