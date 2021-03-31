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
        sleep(1);  
        $countRefund  = self::__curl($baseUrl,$username,$password);
        //print_r($countRefund);die;
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
                    if(isset($responseData->customers)){
                       for($l = 0;$l<count($responseData->customers);$l++){
                          if(isset($responseData->customers[$l]->id)){
                              array_push($customerData,$responseData->customers[$l]);
                          }   
                      }
                    }      
              }
             //  echo count($customerData);die;
              //echo "<PRE>";print_r($customerData);die;
              for($i=0;$i<count($customerData);$i++){
                $orderData=$responseArr = array();
                $lastorderData = array();
                $last_order = '';
                if(!empty($customerData[$i]->last_order_id)){ 
                  $baseUrl = 'https://lord-jameson-dog.myshopify.com/admin/api/2021-01/orders/'.$customerData[$i]->last_order_id.'.json';
                    $lastorderData = self::__curl($baseUrl,$this->username,$this->password);
                }

                if(isset($lastorderData->order)){
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
                echo trim($customerData[$i]->tags);die;
                  if(count($customer) == 0){
                    
                      $values = array('customer_id' => $customerData[$i]->id,'first_name' => $customerData[$i]->first_name,'last_name'=>$customerData[$i]->last_name,'address'=>$add,'mobileno'=>$customerData[$i]->phone,'email'=>$customerData[$i]->email,'customer_created_at'=>$customerData[$i]->created_at,'average_amount'=>round($avgAmount,'2'),'total_order'=>$customerData[$i]->orders_count,'last_order_date'=>$last_order,'customer_tag'=>trim($customerData[$i]->tags));
                      DB::table('tbl_customer')->insert($values);
                  }else{
                    $updateArr = array('first_name' => $customerData[$i]->first_name,'last_name'=>$customerData[$i]->last_name,'address'=>$add,'mobileno'=>$customerData[$i]->phone,'email'=>$customerData[$i]->email,'average_amount'=>round($avgAmount,'2'),'total_order'=>$customerData[$i]->orders_count,'last_order_date'=>$last_order,'customer_tag'=>trim($customerData[$i]->tags));
                       DB::table('tbl_customer')->where('customer_id', $customerData[$i]->id)->update($updateArr);
                  }   
              }
    }

    public function getCustomer(){
        $startDate = date('m/d/Y',strtotime("-30 days"));
        $endDate   = date('m/d/Y',strtotime("now"));

        $oldDateS = date('m/d/Y', strtotime('-1 month', strtotime($startDate)));
        $oldDateE = date('m/d/Y', strtotime('-1 month', strtotime($endDate)));

        $customer = DB::select('select * from tbl_customer where total_order >= 1 and date(customer_created_at) >= "'.date('Y-m-d',strtotime($startDate)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($endDate)).'"');
        if(!empty($customer)){
          $_SESSION['customer'] = $customer;
        }
        $mindate = DB::select('SELECT min(date(customer_created_at)) as mindate from tbl_customer');
        $maxdate = DB::select('SELECT max(date(customer_created_at)) as maxdate from tbl_customer');
        //$finalDate = date("m/d/Y", strtotime($mindate[0]->mindate)).' - '.date("m/d/Y", strtotime($maxdate[0]->maxdate));
       
        $allCustomer = DB::select('select * from tbl_customer where date(customer_created_at) >= "'.date('Y-m-d',strtotime($startDate)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($endDate)).'"');
        $allDTC = DB::select('SELECT * from tbl_customer where FIND_IN_SET("DTC",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($startDate)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($endDate)).'"');
        $allWholesale = DB::select('SELECT * from tbl_customer where FIND_IN_SET("Wholesale",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($startDate)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($endDate)).'"');

        $allCustomerOld= DB::select('select * from tbl_customer where date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'"');
        $allDTCOld = DB::select('SELECT * from tbl_customer where FIND_IN_SET("DTC",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'"');
        $allWholesaleOld = DB::select('SELECT * from tbl_customer where FIND_IN_SET("Wholesale",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'"');



        $allCustomerTags = DB::select('SELECT customer_tag,count(id) as total FROM `tbl_customer` GROUP by customer_tag');
        echo view ('customer_report',['customerData' => $customer,'startDate'=>$startDate,'endDate'=>$endDate,'allCustomer'=>$allCustomer,'allDTC'=>$allDTC,'allWholesale'=>$allWholesale,'allCustomerOld'=>$allCustomerOld,'allDTCOld'=>$allDTCOld,'allWholesaleOld'=>$allWholesaleOld,'allCustomerTags'=>$allCustomerTags,'tab'=>'1']);
    }

    public function getCustomerFilter(){
      $dateAtt = array();
        $dateAtt = explode(" - ",$_POST['daterange']);

        $oldDateS = date('m/d/Y', strtotime('-1 month', strtotime($dateAtt[0])));
        $oldDateE = date('m/d/Y', strtotime('-1 month', strtotime($dateAtt[1])));

        $customer = DB::select('select * from tbl_customer where total_order >= 1 and date(customer_created_at) >= "'.date('Y-m-d', strtotime($dateAtt[0])).'" and date(customer_created_at) <= "'.date('Y-m-d', strtotime($dateAtt[1])).'"');
        if(!empty($customer)){
          $_SESSION['customer'] = $customer;
        }

        $allCustomer = DB::select('select * from tbl_customer where date(customer_created_at) >= "'.date('Y-m-d',strtotime($dateAtt[0])).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($dateAtt[1])).'"');
        $allDTC = DB::select('SELECT * from tbl_customer where FIND_IN_SET("DTC",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($dateAtt[0])).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($dateAtt[1])).'"');
        $allWholesale = DB::select('SELECT * from tbl_customer where FIND_IN_SET("Wholesale",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($dateAtt[0])).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($dateAtt[1])).'"');

        
        $allCustomerOld= DB::select('select * from tbl_customer where date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'"');
        $allDTCOld = DB::select('SELECT * from tbl_customer where FIND_IN_SET("DTC",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'"');
        $allWholesaleOld = DB::select('SELECT * from tbl_customer where FIND_IN_SET("Wholesale",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'"');


          $allCustomerTags = DB::select('SELECT customer_tag,count(id) as total FROM `tbl_customer` GROUP by customer_tag');
          //echo count($allCustomerOld);die;
        echo view ('customer_report',['customerData' => $customer,'startDate'=>$dateAtt[0],'endDate'=>$dateAtt[1],'date'=>$_POST['daterange'],'allCustomer'=>$allCustomer,'allDTC'=>$allDTC,'allWholesale'=>$allWholesale,'allCustomerOld'=>$allCustomerOld,'allDTCOld'=>$allDTCOld,'allWholesaleOld'=>$allWholesaleOld,'allCustomerTags'=>$allCustomerTags,'tab'=>$_POST['tab']]);
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

    public function getTotalTags(Request $request){
      $databyTag=$databyTagOld = array();
        if(!empty($_POST)){
            $startDate = $_POST['startDate'];
            $endDate   = $_POST['endDate'];
            $oldDateS = date('m/d/Y', strtotime('-1 month', strtotime($startDate)));
            $oldDateE = date('m/d/Y', strtotime('-1 month', strtotime($endDate)));

            echo 'SELECT * from tbl_customer where FIND_IN_SET("'.$_POST['tags'].'",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($_POST['startDate'])).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($_POST['endDate'])).'"';die;
            $databyTag = DB::select('SELECT * from tbl_customer where FIND_IN_SET("'.$_POST['tags'].'",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($_POST['startDate'])).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($_POST['endDate'])).'"');

            $databyTagOld = DB::select('SELECT * from tbl_customer where FIND_IN_SET("'.$_POST['tags'].'",customer_tag) and date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'"');
           

            $newstartsData = array();
            $oldstartsData = array();

            $newstartsData = DB::select('SELECT date(customer_created_at) as customer_created_at, count(*) as status FROM tbl_customer where FIND_IN_SET("'.$_POST['tags'].'",customer_tag) AND date(customer_created_at) >= "'.date('Y-m-d',strtotime($startDate)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($endDate)).'" Group by date(customer_created_at)  order By customer_created_at ASC');

            $oldstartsData = DB::select('SELECT date(customer_created_at) as customer_created_at, count(*) as status FROM tbl_customer where FIND_IN_SET("'.$_POST['tags'].'",customer_tag) AND date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'" Group by date(customer_created_at)  order By customer_created_at ASC');

           // echo "<PRE>";print_r($newstartsData);die;
            $new_chart_data = '';
            $totalStarts = array();
            $tStarts = 5;
            foreach($newstartsData as $key => $value){
                $str = ''; 
                $array=explode("-",$value->customer_created_at); 
                     $startDate1 = date('Y-m-d',strtotime($value->customer_created_at));
                     $startDate11 = date('Y-m-d',strtotime($startDate1." -1 Months"));
                     $prevDate = explode("-",$startDate11);
                     $array[1] = $prevDate[1];
                     $array[0] = $prevDate[0];
              
                $str = 'Date.UTC('.$array[0].','.$array[1].','.$array[2].')';
                $new_chart_data .= "[".$str.",".$value->status."], ";
                array_push($totalStarts,$value->status);
                $tStarts = max($totalStarts);
            }

            $new_chart_data = rtrim($new_chart_data,' ,');

            $old_chart_data = '';
            foreach($oldstartsData as $key1 => $value1){
                $strOld = ''; 
                $array_old=explode("-",$value1->customer_created_at); 
                $str = 'Date.UTC('.$array_old[0].','.$array_old[1].','.$array_old[2].')';
                $old_chart_data .= "[".$str.",".$value1->status."], ";
            }
            $old_chart_data = rtrim($old_chart_data,' ,');

            $seriesDataCus = '';
            $seriesDataCus.='[{ name:"Specified Period", data: ['.$new_chart_data.'] }, { name:"Past Period", data: ['.$old_chart_data.'] }]';

            //echo "<PRE>";print_r($allWholesale);die;
            if(!empty($databyTag)){
                $total = (count($databyTag));
            }else{
                $total = '0';
            }
            if(!empty($databyTagOld)){
                $totalOld = (count($databyTagOld));
            }else{
                $totalOld = '0';
            }
            $data = array();
            $data['total'] = $total;
            $data['totalOld'] = $totalOld;
            $data['seriesDataCus'] = $seriesDataCus;
            $data['tStarts'] = $tStarts;
            $data['id'] = trim(str_replace(" ","-",$_POST['tags']));
            echo json_encode($data);exit();
        }
    }

    public function getMapData(Request $request){
        if(!empty($_POST['date'])){
            $dateArr = explode(" - ",$_POST['date']);
            $startDate = $dateArr[0];
            $endDate   = $dateArr[1];
            $oldDateS = date('m/d/Y', strtotime('-1 month', strtotime($startDate)));
            $oldDateE = date('m/d/Y', strtotime('-1 month', strtotime($endDate)));

        $newstartsData = array();
        $oldstartsData = array();

        $newstartsData = DB::select('SELECT date(customer_created_at) as customer_created_at, count(*) as status FROM tbl_customer where date(customer_created_at) >= "'.date('Y-m-d',strtotime($startDate)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($endDate)).'" Group by date(customer_created_at)  order By customer_created_at ASC');

        $oldstartsData = DB::select('SELECT date(customer_created_at) as customer_created_at, count(*) as status FROM tbl_customer where date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'" Group by date(customer_created_at)  order By customer_created_at ASC');

       // echo "<PRE>";print_r($newstartsData);die;
        $new_chart_data = '';
        $totalStarts = array();
        $tStarts = 5;
        foreach($newstartsData as $key => $value){
            $str = ''; 
            $array=explode("-",$value->customer_created_at); 
                 $startDate1 = date('Y-m-d',strtotime($value->customer_created_at));
                 $startDate11 = date('Y-m-d',strtotime($startDate1." -1 Months"));
                 $prevDate = explode("-",$startDate11);
                 $array[1] = $prevDate[1];
                 $array[0] = $prevDate[0];
          
            $str = 'Date.UTC('.$array[0].','.$array[1].','.$array[2].')';
            $new_chart_data .= "[".$str.",".$value->status."], ";
            array_push($totalStarts,$value->status);
            $tStarts = max($totalStarts);
        }

        $new_chart_data = rtrim($new_chart_data,' ,');

        $old_chart_data = '';
        foreach($oldstartsData as $key1 => $value1){
            $strOld = ''; 
            $array_old=explode("-",$value1->customer_created_at); 
            $str = 'Date.UTC('.$array_old[0].','.$array_old[1].','.$array_old[2].')';
            $old_chart_data .= "[".$str.",".$value1->status."], ";
        }
        $old_chart_data = rtrim($old_chart_data,' ,');

        $seriesDataCus = '';
        $seriesDataCus.='[{ name:"Specified Period", data: ['.$new_chart_data.'] }, { name:"Past Period", data: ['.$old_chart_data.'] }]';

        //DTC
        $newDTCData = array();
        $oldDTCData = array();

        $newDTCData = DB::select('SELECT date(customer_created_at) as customer_created_at, count(*) as status FROM tbl_customer where FIND_IN_SET("DTC",customer_tag) AND date(customer_created_at) >= "'.date('Y-m-d',strtotime($startDate)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($endDate)).'" Group by date(customer_created_at)  order By customer_created_at ASC');

        $oldDTCData = DB::select('SELECT date(customer_created_at) as customer_created_at, count(*) as status FROM tbl_customer where FIND_IN_SET("DTC",customer_tag) AND date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'" Group by date(customer_created_at)  order By customer_created_at ASC');

        //echo "<PRE>";print_r($newDTCData);die;
        $new_chart_dataDTC = '';
        $totalDTC = array();
        $tStartsDTC = 5;
        foreach($newDTCData as $key => $value){
            $str = ''; 
            $array=explode("-",$value->customer_created_at); 
                 $startDate1 = date('Y-m-d',strtotime($value->customer_created_at));
                 $startDate11 = date('Y-m-d',strtotime($startDate1." -1 Months"));
                 $prevDate = explode("-",$startDate11);
                 $array[1] = $prevDate[1];
                 $array[0] = $prevDate[0];
          
            $str = 'Date.UTC('.$array[0].','.$array[1].','.$array[2].')';
            $new_chart_dataDTC .= "[".$str.",".$value->status."], ";
            array_push($totalDTC,$value->status);
            $tStartsDTC = max($totalDTC);
        }

        $new_chart_dataDTC = rtrim($new_chart_dataDTC,' ,');

        $old_chart_dataDTC = '';
        foreach($oldDTCData as $key1 => $value1){
            $strOld = ''; 
            $array_old=explode("-",$value1->customer_created_at); 
            $str = 'Date.UTC('.$array_old[0].','.$array_old[1].','.$array_old[2].')';
            $old_chart_dataDTC .= "[".$str.",".$value1->status."], ";
        }
        $old_chart_dataDTC = rtrim($old_chart_dataDTC,' ,');

        $seriesDataDTC = '';
        $seriesDataDTC.='[{ name:"Specified Period", data: ['.$new_chart_dataDTC.'] }, { name:"Past Period", data: ['.$old_chart_dataDTC.'] }]';

        //Wholesale
      
        $newWholesaleData = array();
        $oldWholesaleData = array();

        $newWholesaleData = DB::select('SELECT date(customer_created_at) as customer_created_at, count(*) as status FROM tbl_customer where FIND_IN_SET("Wholesale",customer_tag) AND date(customer_created_at) >= "'.date('Y-m-d',strtotime($startDate)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($endDate)).'" Group by date(customer_created_at)  order By customer_created_at ASC');

        $oldWholesaleData = DB::select('SELECT date(customer_created_at) as customer_created_at, count(*) as status FROM tbl_customer where FIND_IN_SET("Wholesale",customer_tag) AND date(customer_created_at) >= "'.date('Y-m-d',strtotime($oldDateS)).'" and date(customer_created_at) <= "'.date('Y-m-d',strtotime($oldDateE)).'" Group by date(customer_created_at)  order By customer_created_at ASC');

        //echo "<PRE>";print_r($newDTCData);die;
        $new_chart_dataWholesale = '';
        $totalWholesale = array();
        $tStartsWholesale = 5;
        foreach($newWholesaleData as $key => $value){
            $str = ''; 
            $array=explode("-",$value->customer_created_at); 
                 $startDate1 = date('Y-m-d',strtotime($value->customer_created_at));
                 $startDate11 = date('Y-m-d',strtotime($startDate1." -1 Months"));
                 $prevDate = explode("-",$startDate11);
                 $array[1] = $prevDate[1];
                 $array[0] = $prevDate[0];
          
            $str = 'Date.UTC('.$array[0].','.$array[1].','.$array[2].')';
            $new_chart_dataWholesale .= "[".$str.",".$value->status."], ";
            array_push($totalWholesale,$value->status);
            $tStartsWholesale = max($totalWholesale);
        }

        $new_chart_dataWholesale = rtrim($new_chart_dataWholesale,' ,');

        $old_chart_dataWholesale = '';
        foreach($oldWholesaleData as $key1 => $value1){
            $strOld = ''; 
            $array_old=explode("-",$value1->customer_created_at); 
            $str = 'Date.UTC('.$array_old[0].','.$array_old[1].','.$array_old[2].')';
            $old_chart_dataWholesale .= "[".$str.",".$value1->status."], ";
        }
        $old_chart_dataDTC = rtrim($old_chart_dataDTC,' ,');

        $seriesDataWholesale = '';
        $seriesDataWholesale.='[{ name:"Specified Period", data: ['.$new_chart_dataWholesale.'] }, { name:"Past Period", data: ['.$old_chart_dataWholesale.'] }]';


            $data = array();
            $data['seriesDataCus'] = $seriesDataCus;
            $data['tStarts'] = $tStarts;
            $data['seriesDataDTC'] = $seriesDataDTC;
            $data['tStartsDTC'] = $tStartsDTC;
            $data['seriesDataWholesale'] = $seriesDataWholesale;
            $data['tStartsWholesale'] = $tStartsWholesale;
            echo json_encode($data);exit();
        }
    }
}