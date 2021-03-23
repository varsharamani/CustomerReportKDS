<?php 
     $dbhost = "localhost";
         $dbuser = "root";
         $dbpass = "";
         $db = "customerreportkds";
         $conn = new mysqli($dbhost, $dbuser, $dbpass,$db);
         
        if (!$conn) {
           die("Connection failed: " . mysqli_connect_error());
        }

    function __curl($url,$username,$password){
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

    $username='7f5554d7ba97c6b25f130fe7c2ddb140';
        $password = '955c50d7d6c8e9508d814d36124664ed';
        $baseUrl = 'https://lord-jameson-dog.myshopify.com/admin/';
            //$url_custom = $baseUrl.'custom_collections/count.json';
            $url = $baseUrl.'customers/count.json';
            $total = __curl($url,$username,$password);
           //print_r($total);die;
            //echo  $total->count;die;
            $count_c = $total->count/50;
            //echo $count_c;die; 
            $responseArr_all_custom = array();
            for($i=1;$i<=ceil($count_c);$i++){
              $url = $baseUrl.'customers.json?page='.$i;
               $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);  
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                $response = curl_exec($ch);
                curl_close($ch);
                sleep(2);
                $responseArr = json_decode($response);
                array_push($responseArr_all_custom, $responseArr);
            }
             //echo "<PRE>";print_r($responseArr_all_custom);die;
            $customerData = array();
            for($j=0;$j<count($responseArr_all_custom);$j++){
                if(isset($responseArr_all_custom[$j]->customers)){
                     for($k=0;$k<count($responseArr_all_custom[$j]->customers);$k++){
                          array_push($customerData,$responseArr_all_custom[$j]->customers[$k]);
                     }
                } 
             }
           // echo count($customerData);die;
           // echo "<PRE>";print_r($customerData);die;

          for($i=0;$i<count($customerData);$i++){
               $orderData=$responseArr = array();
              /* $baseUrl = 'https://lord-jameson-dog.myshopify.com/admin/customers/'.$customerData[$i]->id.'/orders.json'; */
             
              $lastorderData = array();
               $last_order = '';
              if(!empty($customerData[$i]->last_order_id)){ 
                $baseUrl = 'https://lord-jameson-dog.myshopify.com/admin/orders/'.$customerData[$i]->last_order_id.'.json';
                  $lastorderData = __curl($baseUrl,$username,$password);
            //print_r($lastorderData);die;
              }

              if(!empty($lastorderData)){
               //print_r($lastorderData);die;
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
                $sql = 'select * from tbl_customer where customer_id ='.$customerData[$i]->id;
                $result = $conn->query($sql);
              

              if(mysqli_num_rows($result) == 0){
                $sql = "INSERT INTO tbl_customer (customer_id, first_name, last_name,address,mobileno,email,customer_created_at,average_amount,total_order,last_order_date) VALUES (".$customerData[$i]->id.", '".$customerData[$i]->first_name."', '".$customerData[$i]->last_name."','".$add."',".$customerData[$i]->phone.",'".$customerData[$i]->email."','".$customerData[$i]->created_at."','".$avgAmount."',".$customerData[$i]->orders_count.",".$last_order.")";
                    $conn->query($sql);
                  
              }
             
          }
?>