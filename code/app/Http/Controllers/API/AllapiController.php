<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use Mail;
use PDF;
use App\Mail\MyDemoMail;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Hydrator\NoopHydrator;
use Storage;
use DB;
use Illuminate\Support\Str;
use Log;
use Imagick;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Mailgun\Mailgun;
// use Mailgun\Mailgun;


class AllapiController extends Controller
{

    // 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

                 protected $authenticator;
                 protected $client;
                 protected $account;

    protected $ServiceType = array(
        "US-FC"     =>  "USPS First-Class Mail",
        "US-MM"     =>  "USPS Media Mail",
        "US-PP"     =>  "USPS Parcel Post",
        "US-PM"     =>  "USPS Priority Mail",
        "US-XM"     =>  "USPS Priority Mail Express",
        "US-EMI"    =>  "USPS Priority Mail Express International",
        "US-PMI"    =>  "USPS Priority Mail International",
        "US-FCI"    =>  "USPS First Class Mail International",
        "US-CM"     =>  "USPS Critical Mail",
        "US-PS"     =>  "USPS Parcel Select",
        "US-LM"     =>  "USPS Library Mail"
    );
    // public function __construct()
    // {
    //     // dd("DJDJDJDJ");
    //     $wsdl       = "https://swsim.testing.stamps.com/swsim/swsimv90.asmx?wsdl";
    //     // $integrationID  = "c929ea59-dd49-4329-bd50-ab2d772e5f77";
    //     $integrationID  = "82db5653-21a9-4355-8067-69beaf2f9e27";
    //     $username       = "MDP-001";
    //     $password       = "November2020!";

    //         $this->client = new SoapClient($wsdl);

    //             $authData = [
    //                     "Credentials"   => [
    //                     "IntegrationID"     => $integrationID,
    //                     "Username"          => $username,
    //                     "Password"          => $password
    //                 ]
    //     ];
    //     $this->makeCall('AuthenticateUser', $authData);
    //     // $this->makeCall('AuthenticateUser', $authData));
    //     $this->account = $this->makeCall('GetAccountInfo', ["Authenticator" => $this->authenticator]);
    // }
        public function __construct()
    {

        // $wsdl       = "https://swsim.testing.stamps.com/swsim/swsimv90.asmx?wsdl";
        $wsdl       = "https://swsim.stamps.com/swsim/swsimv90.asmx?wsdl";
        $integrationID  = "82db5653-21a9-4355-8067-69beaf2f9e27";
        // $username       = "MDP-002";
        // $password       = "January2021!";
        $username       = "doxaventures";
        $password       = "D!YkC2S9UFN*Nes";

        $this->client = new SoapClient($wsdl);
        $authData = [
            "Credentials"   => [
                "IntegrationID"     => $integrationID,
                "Username"          => $username,
                "Password"          => $password
            ]
        ];
        $this->makeCall('AuthenticateUser', $authData);
        $this->account = $this->makeCall('GetAccountInfo', ["Authenticator" => $this->authenticator]);
    }

    // protected function makeCall($method, $data) {
    //     $result = $this->client->$method($data);
    //     $this->authenticator = $result->Authenticator;
    //     // dd($result); 
    //     return $result;
    // }

    private function makeCall($method, $data) {

        // if($method == "CreateIndicium")
        // {

        //     // print_r($data);
        // }

        $result = $this->client->$method($data);

        //       if($method == "CreateIndicium")
        // {

        //     // print_r($result);
        // }

        $this->authenticator = $result->Authenticator;

        return $result;
          }



      public function GetRates($FromZIPCode, $ToZIPCode = null, $ToCountry = null, $WeightLb, $Length, $Width, $Height, $PackageType, $ShipDate, $InsuredValue, $ToState = null)
    {
        // dd($WeightLb);
        $data = [
            "Authenticator" => $this->authenticator,
            "Rate"      => [
                "FromZIPCode"   => $FromZIPCode,
                "WeightLb"  => $WeightLb,
                "Length"    => $Length,
                "Width"     => $Width,
                "Height"    => $Height,
                "PackageType"   => $PackageType,
                "ShipDate"  => $ShipDate,
                "InsuredValue"  => $InsuredValue
            ]
        ];
        if ($ToZIPCode == null && $ToCountry != null) {
            $data["Rate"]['ToCountry'] = $ToCountry;
        } else {
            $data["Rate"]['ToZIPCode'] = $ToZIPCode;
        }

        if ($ToState != null) {
            $data["Rate"]['ToState'] = $ToState;
        }
        // dd($data ,"THIS _ IS _DATA");

           $rates = $this->makeCall('getRates', $data)->Rates->Rate;

           // dd(count($rates));

           // dd($rates);

        foreach ($rates as $k => $v) {
            foreach ($v as $kk => $vv) {
                $result[$k][$kk] = $v->$kk;
            }

            $result[$k] =  $result[$k] + array(
                "ServiceType" => $this->ServiceType[$v->ServiceType],
                "Amount" => $v->Amount,
                "PackageType" => $v->PackageType,
                "WeightLb" => $v->WeightLb,
                "Length" => $v->Length,
                "Height" => $v->Height,
                "ShipDate" => $v->ShipDate,
                "DeliveryDate" => property_exists($v, 'DeliveryDate') ? $v->DeliveryDate : 'Unavailable',
                "RateCategory" => $v->RateCategory,
                "ToState" => $v->ToState
            );
            // dd("result",$result);
        }
    return $result;
    }
// private $wsdl       = "https://swsim.testing.stamps.com/swsim/swsimv90.asmx?wsdl";
// private  $integrationID  = "c929ea59-dd49-4329-bd50-ab2d772e5f77";
// private $username       = "MDepot-001";
// private $password       = "May2020!";

public function reateget(Request $request)
{
      // dd($request->all());
		try {
			
        $rates=array();

  $weight = $request->input('weight');
        $height = $request->input('height');
        $country = $request->input('o_country');
        $deliverycountry = $request->input('d_country');
        $zipcode = $request->input('o_zipcode');
        $dzipcode = $request->input('d_zipcode');
        // $mobile = $request->input('mobile');
        $width = $request->input('width');
        $length = $request->input('length');
        $packagetype="Package";
        $insuredvalue="100.00";
        $shipDate = date('Y-m-d');
        // dd($zipcode, $dzipcode,$weight,$length,$width,$height,$packagetype, $shipDate,$insuredvalue);
            $rates  = $this->GetRates($zipcode, $dzipcode, null,$weight,$length,$width,$height,$packagetype, $shipDate,$insuredvalue,null);
            	// dd($rates);
            $requestdata=array();
            $requestdata['weight']=$weight; 
             $requestdata['height']=$height; 
              $requestdata['country']=$country; 
               $requestdata['deliverycountry']=$deliverycountry; 
                $requestdata['zipcode']=$zipcode; 
                 $requestdata['dzipcode']=$dzipcode; 
                  // $requestdata['mobile']=$mobile;
                  $requestdata['width']=$width; 
                  $requestdata['length']=$length; 
                  $requestdata['packagetype']=$packagetype; 
                  $requestdata['insuredvalue']=$insuredvalue; 

                  // dd($rates);

      return response()->json(['data' => $rates,'userdata'=>$requestdata])                       
                        ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                       ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
    


            	} catch (Exception $e) {


			      return response()->json(['data' => $rates,'userdata'=>$e])                       
                        ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                       ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
		}           


    
 //        dd("JSJSJSJSJS");   
 //        // $weight = $request->input('weight');
 //        // $height = $request->input('height');
 //        // $country = $request->input('o_country');
 //        // $deliverycountry = $request->input('d_country');
 //        // $zipcode = $request->input('o_zipcode');
 //        // $dzipcode = $request->input('d_zipcode');
 //        // $mobile = $request->input('mobile');
 //        // $width = $request->input('width');
 //        // $length = $request->input('length');
 //        // $packagetype="Package";
 //        // $insuredvalue="100.00";
 //        // $shipDate = date('Y-m-d');

 //        $weight = $request->input('weight');
 //        $height = $request->input('height');
 //        $country = $request->input('o_country');
 //        $deliverycountry = $request->input('d_country');
 //        $zipcode = $request->input('o_zipcode');
 //        $dzipcode = $request->input('d_zipcode');
 //        $mobile = $request->input('mobile');
 //        $width = $request->input('width');
 //        $length = $request->input('length');
 //        $packagetype="Package";
 //        $insuredvalue="100.00";
 //        $shipDate = date('Y-m-d');
 //        // dd($shipDate);
 //            $rates  = $this->GetRates($zipcode, $dzipcode, null,$weight,$length,$width,$height,$packagetype, $shipDate,$insuredvalue,null);
	// // dd($rates);
 //            $requestdata=array();
 //            $requestdata['weight']=$weight; 
 //             $requestdata['height']=$height; 
 //              $requestdata['country']=$country; 
 //               $requestdata['deliverycountry']=$deliverycountry; 
 //                $requestdata['zipcode']=$zipcode; 
 //                 $requestdata['dzipcode']=$dzipcode; 
 //                  $requestdata['mobile']=$mobile;
 //                  $requestdata['width']=$width; 
 //                  $requestdata['length']=$length; 
 //                  $requestdata['packagetype']=$packagetype; 
 //                  $requestdata['insuredvalue']=$insuredvalue; 



 //      return response()->json(['data' => $rates,'userdata'=>$requestdata])                       
 //                        ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
 //                       ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));

      //return response()->json(['data' => $rates]);


                       
	echo '<pre>';
print_r("====== GET rates ===============");

print_r($rates);

}

     public function getaccountinfo()
    {
        $data = [
            "Authenticator" => $this->authenticator  

        ];
           $rates = $this->makeCall('GetAccountInfo', $data);
       return $rates;
    }

    public function GetPurchaseStatus(Request $request)
    {
        // dd($request->all());
         $data = [
            "Authenticator" => $this->authenticator,
              "PurchasePostage"   => [
            "PurchaseAmount"  => $request->purchaseamount, 
            "ControlTotal" => $request->controltotal
              ],
            "TransactionID"  => $request->TransactionID, 
        ];
           $rates = $this->makeCall('GetPurchaseStatus', $data);
           // dd($rates);
            echo '<pre>';
         print_r("====== GET GetPurchaseStatus ===============");
         print_r($rates);
        return $rates;
    }

    public function PurchasePostage(Request $request)
    {

         $data = [
            "Authenticator" => $this->authenticator,
              "TransactionID"  => $request->TransactionID, 
           "PurchasePostage"      => [
            "PurchaseAmount"  => $request->purchaseamount, 
            "ControlTotal" => $request->controltotal
                    ],
            // 72492724,
            // "ControlTotal" => $controlTotal
         ];

           $rates = $this->makeCall('PurchasePostage', $data);
           dd($rates);
            echo '<pre>';
         print_r("====== GET GetPurchaseStatus ===============");

         print_r($rates);

        return $rates;

    }

    public function CleanseAddress(Request $request)
    {
            $data = [
            "Authenticator" => $this->authenticator,
            "Address"      => [
                "FullName"   => $request->fullname,
                "Company"  => $request->company,
                "Address1" => $request->address1,
                "City"     => $request->city,
                "State"    => $request->state,
                "ZIPCode"   =>  $request->zipcode
            ],
            "FromZIPCode" =>$request->fromzipcode
        ];
                // dd($request->all() ,$data);
           $rates = $this->makeCall('CleanseAddress', $data);
           dd($rates);
            echo '<pre>';
         print_r("====== GET GetPurchaseStatus ===============");
         print_r($rates);
        return $rates;
    }


    		public function getPurchasePostage($ControlTotal)
    		{
    					

    				$data =[
    				  	 "Authenticator" => $this->authenticator,
    				  	 "PurchaseAmount"=>10,
    				  	 "ControlTotal"=>$ControlTotal		
    				];


    				 $rates = $this->makeCall('PurchasePostage', $data);
           // dd($rates);		
    					return $rates;
    		}


    public function createindicium(Request $request)
    {
            // dd($request->all());


// =================== Insert Product in Shopify ==============================

            $sprocess= Str::random(10);
  $data='{
                 "product": {
                        "title": "Shipping Label",
                        "body_html": "THIS label Product ",
                        "vendor": "omtec",
                        "product_type": "Shipjam_omtec",
                        "shop": {
                          "id": 690933842,
                          "name": "Omtec-'.$sprocess.'",
                          "email": "demotesting0909@gmail.com",
                          "domain": "apple.myshopify.com",
                          "province": "California",
                          "country": "US",
                          "address1": "1 Infinite Loop",
                          "zip": "95014",
                          "city": "Cupertino",
                          "source": null,
                          "phone": "1231231234",
                          "latitude": 45.45,
                          "longitude": -75.43,
                          "primary_locale": "en",
                          "address2": "Suite 100",
                          "created_at": "2007-12-31T19:00:00-05:00",
                          "updated_at": "2020-11-04T16:46:58-05:00",
                          "country_code": "US",
                          "country_name": "United States",
                          "currency": "USD",
                          "customer_email": "steve@apple.com",
                          "timezone": "(GMT-05:00) Eastern Time (US & Canada)",
                          "iana_timezone": "America/New_York",
                          "shop_owner": "Steve Jobs",
                          "money_format": "$",
                          "money_wif_currency_format": "$ USD",
                          "weight_unit": "lb",
                          "province_code": "CA",
                          "taxes_included": null,
                          "tax_shipping": null,
                          "county_taxes": true,
                          "plan_display_name": "Shopify Plus",
                          "plan_name": "enterprise",
                          "has_discounts": true,
                          "has_gift_cards": true,
                          "myshopify_domain": "apple.myshopify.com",
                          "google_apps_domain": null,
                          "google_apps_login_enabled": null,
                          "money_in_emails_format": "$",
                          "money_wif_currency_in_emails_format": "$ USD",
                          "eligible_for_payments": true,
                          "requires_extra_payments_agreement": false,
                          "password_enabled": null,
                          "has_storefront": true,
                          "eligible_for_card_reader_giveaway": false,
                          "finances": true,
                          "primary_location_id": 905684977,
                          "cookie_consent_level": "implicit",
                          "visitor_tracking_consent_preference": "allow_all",
                          "force_ssl": true,
                          "checkout_api_supported": true,
                          "multi_location_enabled": false,
                          "setup_required": false,
                          "pre_launch_enabled": false,
                          "enabled_presentment_currencies": [
                            "USD"
                          ]
                        },
                        "status": "active"
                      }
                    }';


                 $collects = json_decode($data, true);

       $solitairesnot = array();
              $solitairesinsert = array();
              $solitairesexists = array();
              $cronjob=0;
                  $i=1;
                // dd($countdata);
              set_time_limit(12000);
        // $data = array(
        //     'last_sync_date' => '01/09/2017',        
        // );

        // $payload = json_encode($data);

           // Prepare new cURL resource

        $ch = curl_init('https://bf0126a229fe2a04c4267624c781b402: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@shipjam.myshopify.com/admin/api/2021-01/products.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            // 'Authorization: Basic ZHNfYXBpX2RpdmluZTpkMXYxbmVANzg5',
            'Content-Length: ' . strlen($data)
        ));
     // Submit the POST request
        $result = curl_exec($ch);
         $err = curl_error($ch);
      // Close cURL session handle
        curl_close($ch);

              if ($err)
        {

            dd($err);
            echo "cURL Error #:" . $err;
        }
        else
        {

               $prodectinsert = json_decode($result);
            // dd($result,$prodectinsert,"THIS IS CRUD CALL RESULT");
        }


  // dd($result,"THIS T");

            // $var = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/products.json','POST',$collects);

          // $prodectinsert = json_decode($var['response']);

                $pricebackend = 0;

               $pricecheck = DB::table('details')
                        ->where('min' , '<=' ,$request->price)
                        ->where('max' , '>=' ,$request->price)
                         // ->orderBy('id', 'desc')
                        ->first();



                         if($pricecheck->is_percentage == "false") 
                         {
                              $pricebackend = $request->price + $pricecheck->amount;

                               // dd($d,"IF MA ",$request->price,$request->price1,$pricecheck->is_percentage);  
                         }
                         else{

                                  $de = ($request->price*$pricecheck->amount)/100;
                                  $pricebackend = $request->price +$de;

                                  // dd($d,"Else ma ",$demo,$demo1,$pricecheck->is_percentage,$de );  
                         }


                      $variant='{
                                    "variant": {
                                    "option1": "Item1'.$pricebackend.'",
                                    "price": "'.round($pricebackend).'",
                                    "requires_shipping":false
                                    }
                                  }';


     // $collectsvariant = json_decode($variant, true);  

     //       $collectsvariantshow = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/products/'.$prodectinsert ->product->id.'/variants.json','POST',$collectsvariant);

             // $p= json_decode($collectsvariantshow['response']);



          $ch = curl_init('https://bf0126a229fe2a04c4267624c781b402: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@shipjam.myshopify.com/admin/api/2021-01/products/'.$prodectinsert ->product->id.'/variants.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $variant);
        // Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            // 'Authorization: Basic ZHNfYXBpX2RpdmluZTpkMXYxbmVANzg5',
            'Content-Length: ' . strlen($variant)
        ));
     // Submit the POST request
        $resultvaiants = curl_exec($ch);
         $err = curl_error($ch);
      // Close cURL session handle
        curl_close($ch);

              if ($err)
        {

            dd($err);
            echo "cURL Error #:" . $err;
        }
        else
        {

            // dd($resultvaiants,"THIS IS CRUD CALL RESULTvaiants");


 $p= json_decode($resultvaiants);
               // $prodectinsert = json_decode($resultvaiants['response']);

        }              

   $noIsert = array();
        $curl = curl_init();
     
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://bf0126a229fe2a04c4267624c781b402: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@shipjam.myshopify.com/admin/api/2021-01/products/'.$prodectinsert ->product->id.'.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(

                // "content-type: application/json",
                // 'Authorization: Basic ZHNfYXBpX2RpdmluZTpkMXYxbmVANzg5',
            ) ,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err)
        {
            dd($err);
            echo "cURL Error #:" . $err;
        }
        else
        {
          $getproduct_show= json_decode($response);
        // dd($response);
        };

      // dd($p,"prodectinsert",$prodectinsert,"getproduct_show",$getproduct_show);              

//            $getproduct = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/products/'.$prodectinsert ->product->id.'.json','GET',$query = array());

// $getproduct_show= json_decode($getproduct['response']);

           // $locationsget = shopify_call('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','ship-jam.myshopify.com','/admin/api/2021-01/locations.json','GET',$query = array());

           //                  $plocationsget= json_decode($locationsget['response']);

   $noIsert = array();
        $curl = curl_init();

             curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://bf0126a229fe2a04c4267624c781b402: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@shipjam.myshopify.com/admin/api/2021-01/locations.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(

                // "content-type: application/json",
                // 'Authorization: Basic ZHNfYXBpX2RpdmluZTpkMXYxbmVANzg5',
            ) ,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err)
        {
            dd($err);
            echo "cURL Error #:" . $err;
        }
        else
        {

          $plocationsget= json_decode($response);
        // dd($response);
        }

          // dd($plocationsget,$getproduct_show,$getproduct_show->product->variants[1]->inventory_item_id);
          
                    $variantleval='{
                                            "location_id": '.$plocationsget->locations[0]->id.',
                                          "inventory_item_id": '.$getproduct_show->product->variants[1]->inventory_item_id.',
                                          "available_adjustment": 10
                                         }';

                                        
     // $collectsvariant = json_decode($variant, true);  

           // $collectsvariantshow = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/inventory_levels/adjust.json','POST',$collectsvariant);

          $ch = curl_init('https://bf0126a229fe2a04c4267624c781b402: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@shipjam.myshopify.com/admin/api/2020-10/inventory_levels/adjust.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $variantleval);
        // Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            // 'Authorization: Basic ZHNfYXBpX2RpdmluZTpkMXYxbmVANzg5',
            'Content-Length: ' . strlen($variantleval)
        ));
     // Submit the POST request
        $resultvaiantsleval = curl_exec($ch);
         $err = curl_error($ch);
      // Close cURL session handle
        curl_close($ch);

              if ($err)
        {
            dd($err);
            echo "cURL Error #:" . $err;
        }
        else
        {
            // dd($resultvaiants,"THIS IS CRUD CALL RESULTvaiants");
          $p= json_decode($resultvaiantsleval);
               // $prodectinsert = json_decode($resultvaiants['response']);
        }

         // dd($p,"prodectinsert",$prodectinsert,"getproduct_show",$getproduct_show,"plocationsget",$plocationsget,$plocationsget->locations[0]->id,$resultvaiantsleval,$p);                  




                


             // $p= json_decode($collectsvariantshow['response']);



                    $productid=json_encode($prodectinsert->product->id);
                    $inventoryitemid=json_encode($getproduct_show->product->variants[1]->inventory_item_id);
                    $variantsid=json_encode($getproduct_show->product->variants[1]->id);


                        $insertid= DB::table("orderprocess")
                                    ->insertGetId([

                                              'weight'=>$request->weight, 
                                              'height'=>$request->height,
                                              'deliverycountry'=>$request->deliverycountry,
                                              'zipcode'=>$request->zipcode,
                                              'dzipcode'=>$request->dzipcode,
                                              'mobile'=>$request->mobile,
                                              'width'=>$request->width,
                                              'length'=>$request->length,
                                              'packagetype'=>$request->packagetype,
                                              'insuredvalue'=>$request->insuredvalue,
                                              'userid'=>$request->userid,
                                              'r_country'=>$request->r_country,
                                              'r_postal_code'=>$request->r_postal_code,
                                              'r_city'=>$request->r_city,
                                              'r_state'=>$request->r_state,
                                              'r_address_1'=>$request->r_address_1,
                                              'r_address_2'=>$request->r_address_2,
                                              'r_name'=>$request->r_name,
                                              'r_company'=>$request->r_company,
                                              'r_contact'=>$request->r_contact,
                                              // 'r_email'=>$request->r_email,
                                              'r_url'=>$request->r_url,
                                              'courier_id'=>$request->courier_id,
                                              'price'=>$request->price,
                                              'productid' =>$productid,
                                              'inventory_item_id'=>$inventoryitemid,
                                              'variantsid' =>$variantsid,
                                                  ]);

//         
                     // dd($rates);         

      return response()->json(['data' => $getproduct_show->product->variants[1]->id])                         
                        ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                       ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));


           // dd($plocationsget->locations[0]->id);


                        // dd($getproduct ,$getproduct_show->product->variants[1]->inventory_item_id);


     // // https://{shop}.myshopify.com/admin/api/2021-01/inventory_levels/adjust.json

      // $collectsvariantshow = shopify_call('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','ship-jam.myshopify.com','/admin/api/2021-01/inventory_levels/adjust.json','POST',$collectsvariant);

      //   $p= json_decode($collectsvariantshow['response']);

            

           // print_r($p);


            // dd($p);

                                 // $variant='{
                                 //            "location_id": 6884556842,
                                 //          "inventory_item_id": '.$prodectinsert ->product->variants[0]->inventory_item_id.',
                                 //          "inventory_quantity": 10
                                 //         }';
                                         
                         

           // $inventorylevels = shopify_call('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','ship-jam.myshopify.com','/admin/api/2021-01/locations/36678631618/inventory_levels.json','GET',$query = array());

           //        $plevels= json_decode($inventorylevels['response']);



            // dd($inventorylevels,$plevels);
           

                // dd("DEMo available",$p,$prodectinsert ,$locationsget, "HELLO MODI");







      // $pshow = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/products/'.$prodectinsert ->product->id.'.json','GET',$collectsvariant);

            
  // $p= json_decode($pshow['response']);

            // dd($pshow,$p->product->variants[1]->id);



            // dd("variants APi",$p->variant->id);


            
  //                                       $checkout ='{
  //                                                 "checkout": {
  //                                                   "line_items": [      
  //                                               "variant_id":'.$prodectinsert ->product->variants[0]->id.',
  //                                                       "quantity": 2
  //                                                   ]
  //                                                 }
  //                                               }';

  //     $collectscheckouts = json_decode($checkout, true);

  //     $democheck = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/checkouts.json','POST',$collectscheckouts);  

  //   $democheck1 = json_decode($democheck['response'], true);

  // $democheck222 = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/checkouts/b07d442c2084c10589ad08c6f8525d0b.json','GET',$collectscheckouts);
                         


  //   $democheck1 = json_decode($democheck222['response'], true);


  //     $democheck22_all = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/checkouts.json','GET',$collectscheckouts);


  //     $democheck1_all = json_decode($democheck22_all['response'], true);
      

                // dd($prodectinsert ->product->variants[0]->id,$democheck1['checkout'],$democheck22_all ,$democheck1_all);
 // =================== End Insert Product in Shopify ==============================


  

// $to = "demotesting0909@gmail.com";
// $subject = "SHIJAM EMAIL";

// $message = "
// <html>
// <head>
// <title>Shipjam </title>
// </head>
// <body>
// <p>".$request->r_name."!</p>
// <p> URL :- ".$url."</p>
// <table>
// <tr>
// <th>NAME</th>
// </tr>
// <tr>
// <td>".$request->r_name."</td>
// </tr>
// </table>
// </body>
// </html>";

// // Always set content-type when sending HTML email
// $headers = "MIME-Version: 1.0" . "\r\n";
// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// // More headers
// $headers .= 'From: <demotesting0909@gmail.com>' . "\r\n";
// $headers .= 'Cc: demotesting0909@gmail.com' . "\r\n";

// mail($to,$subject,$message,$headers);


    } 


    public function pdf($iname)
    {

$data = ['title' => 'Welcome to omtecweb.com',
                  'img'=>$iname
                    ];
        $pdf = PDF::loadView('emails.myDemoMail', $data);
        // dd($_SERVER['SERVER_NAME']);
            // return PDF::loadFile(public_path().'/emails/myDemoMail')->save($img)->stream('downloadDMEO.pdf');
         return $pdf->download($iname.'.pdf');
        // dd($pdf);
    }


    public function CancelIndicium($id)
    {
              // dd($id);
                   $data = [
            "Authenticator" => $this->authenticator,
            "StampsTxID"  => $id, 
        ];

           $rates = $this->makeCall('CancelIndicium', $data);

            

            $delete= DB::table("label")->where("StampsTxID",$id)->delete();


               return response()->json(['data' => $delete])                       
                        ->header("Access-Control-Allow-Origin", config('cors.allowed_origins'))
                       ->header("Access-Control-Allow-Methods", config('cors.allowed_methods'));
    }


        public function register(Request $request)
    {  

            dd("DKDKKDKDKD");
    }

             public function demoorder()
    {
      try {



                  $demo = 41.75;
                 $demo1 = 41.75;

                      // $prodectid=json_encode($demo);
                  

                  $d = DB::table('details')
                        ->where('min' , '<=' ,$demo)
                        ->where('max' , '>=' ,$demo)
                         // ->orderBy('id', 'desc')
                        ->first();



                         if($d->is_percentage == "false") 
                         {
                              $demo = $demo + $d->amount;

                               dd($d,"IF MA ",$demo,$demo1,$d->is_percentage);  
                         }
                         else{

                                  $de = ($demo*$d->amount)/100;
                                  $demo = $demo +$de;

                                  dd($d,"Else ma ",$demo,$demo1,$d->is_percentage,$de );  
                         }



                              // ->where('shift_time_start','<',$waktu[1])
                              // ->where('shift_time_end','>',$waktu[1])


                     


$url = "https://swsim.testing.stamps.com/Label/Label3.ashx/label-200.png?AQAAADFs4cJD9NgIJFgeHb5l5jxzlqldjts6JQkE-lZ4nG1RTWgTQRh9m9k0zU-btE0rXsJowRp_0tlNdrdbq3a7JEFNs6GbeBCJpk0o1TZR8SIeWhRFCr3oQSjiQejBgyiC4MF6CbQ9CIIKCgqCh4IXUbw7ThsQQQcew3zf-968eWPXhpF4USX3ntzMPlifXItc1Wfdkx-vPdxVshemZ14-Hrw_cApAWWB6leHV04uQgDgGQKDCJ0GD3BYWtYW2ZufPD-07QCSlDzK86D3jlTgHmK66txOvpczmHopNFcuTKenGWWlbD4LpAZ6vdcMwGLs-v7Rc-lyOfrWNd6FH78-trJQ3jm_c-WG9uRs__eltOTb67df3xtJ6OFx49kWTQLwmSzEtig5g3u6Fj8MCDkH27BPCpkARMt8P0rllgrEkS2oaM9kRxAj-WknBHBFGDguMIlAfAYYgk6Noj8YABf6WV8CArwvDkKWdYuIAIv0iv2A27WQy1MoXnTzn_mpt7kIjYTvjPYpqaia1nQkrR4sT6TQt5EQewZzjCnI2nUu7HtsSL2C6LhtMZRgUoXYJHGwJ-93GXI2ONapXOOfdyZTKqDNVq9RpoXLpPB2bFdWQW6lfrtDxRn1mqtJSE3lwzkBkxlgqALK7uSgmt29pLpqiqynKkGlqOjPFSdENzQyC9P2_EwIJl9yCK_xb9olj-Szt7xAGI38-_FZPK648Alv5xFvb3n_HfgO2vZQn";

     $iname=date("Ymdhis");
                   $img = $iname.'.jpg';  
          // Function to write image into file 
          // file_put_contents($img, file_get_contents($url));


                   // $d= $this->Thumbnail($url, $img);

                   $filename = $img;
                  $width = 550;
                  $height = 900;
                    $image = ImageCreateFromString(file_get_contents($url));
 // calculate resized ratio
 // Note: if $height is set to TRUE then we automatically calculate the height based on the ratio
 $height = $height === true ? (ImageSY($image) * $width / ImageSX($image)) : $height;
 // create image 
 $output = ImageCreateTrueColor($width, $height);
 ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));
 // save image
 ImageJPEG($output, $filename, 95);













    $filenamerotate='';
    $targetFolder = '';

    $targetPath = $targetFolder;

  $filename = 'hello.jpg';
$degrees = 90;

// Content type
header('Content-type: image/jpeg');

// Load
$source = imagecreatefromjpeg($img);

// Rotate
  $filenamerotate =$targetPath .round(microtime(true)).'.jpg';
$rotate = imagerotate($source, $degrees, 0);

// Output

  imagejpeg($rotate, $filenamerotate);
// imagejpeg($rotate);

// // Free the memory
// imagedestroy($source);
// imagedestroy($rotate);


// die("dkdkdkdk");

     $src = imagecreatefromjpeg($filenamerotate);
        $dst = imagecreatefromjpeg('demo3.jpg');

        imagecopymerge($dst, $src, 100, 10, 0, 0, 900, 550, 100);
        header('Content-type: image/jpeg');

          $filename =$targetPath .round(microtime(true)).'_SHIPJAM'.'.jpg';
      imagejpeg($dst, $filename);
      imagedestroy($dst);

      $show=$filename;



          $pdf = PDF::loadView('pdf', compact('show'));


           $pdf->save(public_path($filename.'.pdf'));
        
        // return $pdf->download('disney.pdf');




dd("DJDJDJDJDJDJD");








      // <td class="emailcolsplit" align="left" valign="top" width="42%" style="padding-left:17px;max-width:231px;">
      //                                       <a href="#"><img src="http://placekitten.com/305/255" alt="Supporting image 1" style="display:block; width:100% !important; height:auto !important;border-radius:8px;" border="0"></a></td>


//     $details = ' <body style="width:100% !important; margin:0 !important; padding:0 !important; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; background-color:#FFFFFF;">

//                    <table bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="25" width="100%">
//                         <tr>
//                             <td width="100%" bgcolor="#ffffff" style="text-align:left;">
//                               <p style="color:#0a1c6b; font-family:Arial, Helvetica, sans-serif; font-size:25px; line-height:19px; margin-top:0; margin-bottom:20px; padding:0; font-weight:normal;">
//                                     Great news, your shipping label is ready. Here"s your next step.                             
//                                 </p>
//                               <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:18px; line-height:19px; margin-top:0; margin-bottom:20px; padding:0; font-weight:normal;">
//                                     Shipjam recommends shipping within 2 days, the handling option you selected in your listing.
//                                 </p>
//                                     <table border="0" cellspacing="0" cellpadding="0" width="38%" class="emailwrapto100pc">
//                                       <tr>
//                                         <td class="emailcolsplit" align="left" valign="top" width="58%"  bgcolor="#ffffff" style="text-align:center;"><a style="font-weight:bold; text-decoration:none;" href="http://phplaravel-529849-1688208.cloudwaysapps.com/20210226050011.png"><div style="display:block; max-width:100% !important; width:93% !important; height:auto !important;background-color:#2489B3;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;border-radius:8px;color:#ffffff;font-size:24px;font-family:Arial, Helvetica, sans-serif;">Print a Shipping Label </div></a></td>
//                                         </tr>
//                                     </table>
//                             </td>
//                         </tr>
//                    </table>
//                    <table bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="25" width="100%">
//                         <tr>
//                             <td width="100%" bgcolor="#ffffff" style="text-align:left;">

//                                  <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:14px; margin-top:0; margin-bottom:15px; padding:0; font-weight:normal;">
//                                   You are receiving this email based on your purchase of a shipping label.
//                                 </p>



//                               <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:14px; margin-top:0; margin-bottom:15px; padding:0; font-weight:normal;">
//                                    We dont check this mailbox, so please dont reply to this message. If you have a question, go to <a style="color:#2489B3; font-weight:bold; text-decoration:underline;" href="#">Help & Contact.</a> 
//                                 </p>
//                               <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:14px; margin-top:0; margin-bottom:15px; padding:0; font-weight:normal;">
//                                     ©2021 Shipjam.com., 2885 Sanford Ave SW, Ste 14200, Grandville, MI 49418<br>
//                                 </p>
//                             </td>
//                         </tr>
//                     </table>
//                 </td>
//             </tr>
//         </table>
//         </div>
//     </td>
//   </tr>
// </table> 
// </body>
//       '; 

// // dd("kdkdkdkdkd",$rates);
// $mail = new PHPMailer(true);

// // $mail = new PHPMailer\PHPMailer\PHPMailer(true);

//     // Server settings
//     $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
//     $mail->isSMTP();                                            // Send using SMTP
//     $mail->Host       = 'smtp.mailgun.org';                    // Set the SMTP server to send through
//     $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
//     $mail->Username   = 'postmaster@mg.shipjam.com';                     // SMTP username
//     $mail->Password   = '20b7cf9a6aa9df4d46b44af1efee32f4-77751bfc-436022f5';                               // SMTP password
//     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
//     $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
//     //Recipients
//     $mail->setFrom('postmaster@mg.shipjam.com', 'Mailer');
//     $mail->addAddress("demotesting0909@gmail.com");     // Add a recipient
//     // $mail->addAddress('kalpesh@hastishah.com');
//     // $mail->addAddress('tarang027@gmail.com');               // Name is optional
//                    // Name is optional
//     // $mail->addReplyTo('jigar@omtecweb.com', 'Information');
//     // $mail->addCC('tarang027@gmail.com');
//     // $mail->addBCC('surypal@hastishah.com');
//     // $mail->addAttachment('/'.$img);         // Add attachments
//     // $mail->addAttachment('/20210210083504.png', 'new.jpg');    // Optional name
// // dd($mail);
//     // Content
//     $mail->isHTML(true);                                  // Set email format to HTML
//     $mail->Subject = 'Shipjam.com';
//     $mail->Body    = $details;
//     $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

//     $mail->send();






// dd("HELLOD");


//     $img="20210210083504.png";
// $files = glob($_SERVER["DOCUMENT_ROOT"]."/".$img);


            $ddd=DB::table('orderp')->where('id',668)->first();
              $d= json_decode($ddd->order);
              // dd($d);
               $prodectid=json_encode($d->line_items[0]->product_id);
                 
                 				$demogetcount=DB::table('orderprocess')
                                              ->where("productid",$prodectid)
                                              ->first();
// dd("dkdkdkdkdk",$d->order_number);


                                              	// dd($demogetcount);


                // dd($d->order_number,$d,$d->line_items[0]->product_id,$prodectid,$demogetcount,$d->contact_email,$d->id);
                                              $getaccountinfo=$this->getaccountinfo();
                                                // dd($getaccountinfo->AccountInfo->PostageBalance->ControlTotal);
                                $ControlTotal=$getaccountinfo->AccountInfo->PostageBalance->ControlTotal;
                        $getPurchasePostage=$this->getPurchasePostage($ControlTotal);
                    $TransactionID=$getPurchasePostage->TransactionID;
                      // dd($TransactionID);
                // dd($getaccountinfo,$getPurchasePostage,$ControlTotal);
             $shipDate = date('Y-m-d');

             $s= date('Y-m-d', strtotime($shipDate. ' + 2 days'));
                   $st= Str::random(4);
             $data = [
            "Authenticator" => $this->authenticator,
            "IntegratorTxID"=>"82db5653d".$st."-21a9-4355-8067-69beaf2f9e27",
            "Rate"=>[
                    "FromZIPCode"=>$demogetcount->zipcode,
                    "ToZIPCode"=>$demogetcount->dzipcode,
                    // "Amount"=>$request->price,
                    "ServiceType"=>"US-PM",
                    "DeliverDays"=>"1-1",
                    "WeightLb"=>$demogetcount->weight,
                    "WeightOz"=>$demogetcount->weight,
                    "PackageType"=>$demogetcount->packagetype,
                    "ShipDate"=>$s,
                    "InsuredValue"=>$d->total_price,
                    "RectangularShaped"=>false
                  ],
             "From"=>[
                    // "FullName"=>$demogetcount->r_name,
                    // "Address1"=>$demogetcount->r_address_1,
                    // "City"=>$demogetcount->r_city,    
                    // "State"=>$demogetcount->r_state,
                    // "ZIPCode"=>$demogetcount->r_postal_code

             		"FullName"=>"ShipJam.com",
                    "Address1"=>"14271 Corporate Drive, Ste C",
                    "City"=>"Garden Grove",    
                    "State"=>"CA",
                    "ZIPCode"=>"92843"


                    ],
              "To"=>[
                    // "FullName"=>$d->billing_address->first_name." ".$d->billing_address->last_name,  
                    // "NamePrefix"=>"GEOFF",
                    // 'Company'=>$d->billing_address->company,
                    // "Address1"=>$d->billing_address->address1,  
                    // "City"=>$d->billing_address->province,
                    // "State"=>$d->billing_address->country_code,
                    // "ZIPCode"=>$demogetcount->dzipcode,
              	   	    "FullName"=>$demogetcount->r_name,  
                    "NamePrefix"=>"GEOFF",
                    'Company'=>$demogetcount->r_company,
                    "Address1"=>$demogetcount->r_address_1,  
                    "City"=>$demogetcount->r_city,
                    "State"=>$demogetcount->r_state,
                    "ZIPCode"=>$demogetcount->r_postal_code,
                    "ZIPCodeAddOn"=>"0000",
                    "DPB"=>59,
                    "CheckDigit"=>6,
                    "Province"=>"",
                    "PostalCode"=>"",
                    "Country"=>"",
                    "Urbanization"=>"",
                    "PhoneNumber"=>"",
                    "Extension"=>""
                    // ,
                    // "CleanseHash"=>"7SWYAzuNh82cWhIQyRFXRNa71HFkZWFkYmVlZg==20070513"
                    ]         
            ];
                        $rates = $this->makeCall('CreateIndicium', $data);
                        // dd($rates,"dkdkdkdk");
                           $dataget= $rates->Rate;
                      // dd($rates ,$dataget);
                       $url = $rates->URL;
                      $urlinsert = $url ;  

                    $iname=date("Ymdhis");
          $img = $iname.'.png';  
          // Function to write image into file 
          // $dataimh=file_put_contents($img, file_get_contents($url)); 




$d= $this->Thumbnail($url, "demojigar.jpg");




          dd( $d ,"DDDJJDJDJDJD");



                                                    $choseorderID = DB::table('label')
                                                    ->insertGetId([
                                                            "IntegratorTxID"=>$rates->IntegratorTxID,
                                                            "TrackingNumber"=>$rates->TrackingNumber,
                                                            "FromZIPCode"=>$dataget->FromZIPCode,   
                                                            "ToZIPCode"=>$dataget->ToZIPCode,
                                                            "Amount"=>$d->total_price,
                                                            "ServiceType"=>$dataget->ServiceType,
                                                            "DeliverDays"=>$dataget->DeliverDays,
                                                            "WeightLb"=>$dataget->WeightLb,
                                                            "WeightOz"=>$dataget->WeightOz,
                                                            "PackageType"=>$dataget->PackageType,
                                                            "ShipDate"=>$dataget->ShipDate,
                                                            "DeliveryDate"=>$dataget->DeliverDays,
                                                            "insuredvalue"=>$demogetcount->price,
                                                            "RectangularShaped"=>"false",
                                                            "FullName"=>$demogetcount->r_name,
                                                            "Address1"=>$demogetcount->r_address_1,
                                                            "City"=>$demogetcount->r_city,    
                                                            "State"=>$demogetcount->r_state,
                                                            "ZIPCode"=>$demogetcount->r_postal_code,
                                                            'Company'=>$demogetcount->r_company,
                                                            "toAddress1"=>$demogetcount->r_address_1,
                                                            "toCity"=>"LOS ANGELES",
                                                            "toState"=>"CA",
                                                            "ZIPCodeAddOn"=>7020,
                                                            "DPB"=>59,
                                                            "CheckDigit"=>6,
                                                            "StampsTxID"=>$rates->StampsTxID,
                                                            "url"=>$url,
                                                            "RateCategory"=>$dataget->RateCategory,
                                                            "email"=>$d->contact_email,
                                                            "img"=>$img,
                                                            // "gateway"=>$demogetcount->gateway,
                                                            // "total_price"=>$demogetcount->total_price,
                                                            // "subtotal_price"=>$demogetcount->subtotal_price,
                                                            // "checkout_id"=>$demogetcount->checkout_id,
                                                            // "currency"=>$demogetcount->currency
                                                            // "productID"=>$prodectid
                                                           ]);    



	$details='<html> 
    <head> 
        <title>Welcome to Shipjam</title> 
    </head> 
    <body>
   
        <table cellspacing="0" style="border: 2px dashed #000000; width: 60%; font-size:17px;"> 
            <tr> 
                <th>FullName :- </th><td>'.$d->billing_address->first_name." ".$d->billing_address->last_name.'</td> 
            </tr> 
             <tr> 
                <th>Url :</th><td>'.$url.'</td> 
            </tr> 
        </table> 
    </body>
    </html>';

// dd("kdkdkdkdkd",$rates);
$mail = new PHPMailer(true);

// $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.mailgun.org';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'postmaster@mg.shipjam.com';                     // SMTP username
    $mail->Password   = '20b7cf9a6aa9df4d46b44af1efee32f4-77751bfc-436022f5';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    //Recipients
    $mail->setFrom('postmaster@mg.shipjam.com', 'Mailer');
    $mail->addAddress("demotesting0909@gmail.com");     // Add a recipient
    // $mail->addAddress('kalpesh@hastishah.com');
    // $mail->addAddress('tarang027@gmail.com');               // Name is optional
                   // Name is optional
    // $mail->addReplyTo('jigar@omtecweb.com', 'Information');
    // $mail->addCC('tarang027@gmail.com');
    // $mail->addBCC('surypal@hastishah.com');
    // $mail->addAttachment('/'.$img);         // Add attachments
    // $mail->addAttachment('/20210210083504.png', 'new.jpg');    // Optional name
// dd($mail);
    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Shipjam.com';
    $mail->Body    = $details;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    // echo 'Message has been sent';


} catch (Exception $e) {

  $mail = new PHPMailer(true);

// $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.mailgun.org';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'postmaster@mg.shipjam.com';                     // SMTP username
    $mail->Password   = '20b7cf9a6aa9df4d46b44af1efee32f4-77751bfc-436022f5';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    //Recipients
    $mail->setFrom('postmaster@mg.shipjam.com', 'Mailer');
    $mail->addAddress('demotesting0909@gmail.com', 'Testing-mail');     // Add a recipient
    // $mail->addAddress('kalpesh@hastishah.com');
    // $mail->addAddress('tarang027@gmail.com');               // Name is optional
                   // Name is optional
    // $mail->addReplyTo('jigar@omtecweb.com', 'Information');
    // $mail->addCC('tarang027@gmail.com');
    // $mail->addBCC('surypal@hastishah.com');
    // $mail->addAttachment('C:/Users/om/Desktop/img/20210201120215.png');         // Add attachments
    // $mail->addAttachment('C:/Users/om/Desktop/img/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Shipjam.com';
    $mail->Body    = 'API ERROR ';
    $mail->AltBody = 'API IS NOT WROKING ';

    $mail->send();
    // echo 'Message has been sent';


    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}



                // dd($rates,$d,$d->line_items[0]->product_id,$d->shipping_address,"HELLO DMODI",$prodectid,$demogetcount);

    }  


        public function orderprocess(Request $request)
    {
      try {
              $demoget=json_encode($request->all());
              $d=json_decode($demoget);
              $prodectid=json_encode($d->line_items[0]->product_id);

      
                             $gpid=DB::table('orderp')
                            ->insertGetId([
                                          'order'=>$demoget,
                                          'Productid'=>json_encode($d->line_items[0]->product_id)
                                          ]);

                                      // $p=DB::table('orderp')->where('id',$gpid)->first();

                                        $ddd=DB::table('orderp')->where('id',$gpid)->first();
                                        $d= json_decode($ddd->order);
              // dd($d);
                                        $prodectid=json_encode($d->line_items[0]->product_id);

                                    // $demogetcount=DB::table('orderprocess')
                                    //         ->where("productid",$p->Productid)
                                    //         ->first();

                                          $demogetcount=DB::table('orderprocess')
                                            ->where("productid",$prodectid)
                                            ->first();

// a.nav-link {
//     background: transparent !important;
// }
                  $getaccountinfo=$this->getaccountinfo();
                  $ControlTotal=$getaccountinfo->AccountInfo->PostageBalance->ControlTotal;
                  $getPurchasePostage=$this->getPurchasePostage($ControlTotal);
                // dd($getPurchasePostage);
                    $TransactionID=$getPurchasePostage->TransactionID;
             $shipDate = date('Y-m-d');

             $s= date('Y-m-d', strtotime($shipDate. ' + 2 days'));
             
             $st= Str::random(4);

             $data = [
            "Authenticator" => $this->authenticator,
            "IntegratorTxID"=>"82db5653d".$st."-21a9-4355-8067-69beaf2f9e27",
            "Rate"=>[
                    "FromZIPCode"=>$demogetcount->zipcode,
                    "ToZIPCode"=>$demogetcount->dzipcode,
                    // "Amount"=>$request->price,
                    "ServiceType"=>"US-PM",
                    "DeliverDays"=>"1-1",
                    "WeightLb"=>$demogetcount->weight,
                    "WeightOz"=>$demogetcount->weight,
                    "PackageType"=>$demogetcount->packagetype,
                    "ShipDate"=>$s,
                    "InsuredValue"=>$d->total_price,
                    "RectangularShaped"=>false
                  ],
             "From"=>[
                    // "FullName"=>$demogetcount->r_name,
                    // "Address1"=>$demogetcount->r_address_1,
                    // "City"=>$demogetcount->r_city,    
                    // "State"=>$demogetcount->r_state,
                    // "ZIPCode"=>$demogetcount->r_postal_code

                    "FullName"=>"ShipJam.com",
                    "Address1"=>"14271 Corporate Drive, Ste C",
                    "City"=>"Garden Grove",    
                    "State"=>"CA",
                    "ZIPCode"=>"92843"
                    ],
              "To"=>[

              	 // "FullName"=>$d->billing_address->first_name." ".$d->billing_address->last_name,  
                //     "NamePrefix"=>"GEOFF",
                //     'Company'=>$d->billing_address->company,
                //     "Address1"=>$d->billing_address->address1,  
                //     "City"=>$d->billing_address->province,
                //     "State"=>$d->billing_address->country_code,
                //     "ZIPCode"=>$demogetcount->dzipcode,

              	    "FullName"=>$demogetcount->r_name,  
                    "NamePrefix"=>"GEOFF",
                    'Company'=>$demogetcount->r_company,
                    "Address1"=>$demogetcount->r_address_1,  
                    "City"=>$demogetcount->r_city,
                    "State"=>$demogetcount->r_state,
                    "ZIPCode"=>$demogetcount->r_postal_code,
                    "ZIPCodeAddOn"=>"0000",
                    "DPB"=>59,
                    "CheckDigit"=>6,
                    "Province"=>"",
                    "PostalCode"=>"",
                    "Country"=>"",
                    "Urbanization"=>"",
                    "PhoneNumber"=>"",
                    "Extension"=>""
                    // ,
                    // "CleanseHash"=>"7SWYAzuNh82cWhIQyRFXRNa71HFkZWFkYmVlZg==20070513"
                    ]         
            ];
                        $rates = $this->makeCall('CreateIndicium', $data);

                      $dataget= $rates->Rate;
                      // dd($rates ,$dataget);
                       $url = $rates->URL;
                      $urlinsert = $url; 
                    $iname=date("Ymdhis");
					         $img = $iname.'.jpg';  
					// Function to write image into file 
					// file_put_contents($img, file_get_contents($url));


                   // $d= $this->Thumbnail($url, $img);

                   $filename = $img;
                  $width = 550;
                  $height = 900;
                    $image = ImageCreateFromString(file_get_contents($url));
 // calculate resized ratio
 // Note: if $height is set to TRUE then we automatically calculate the height based on the ratio
 $height = $height === true ? (ImageSY($image) * $width / ImageSX($image)) : $height;
 // create image 
 $output = ImageCreateTrueColor($width, $height);
 ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));
 // save image
 ImageJPEG($output, $filename, 95);

    //     $targetFolder = '';

    // $targetPath = $targetFolder;

    //  $src = imagecreatefromjpeg($filename);
    //     $dst = imagecreatefromjpeg('Untitled-4.jpg');

    //     imagecopymerge($dst, $src, 0, 0, 0, 0, 816, 528, 100);
    //     header('Content-type: image/jpeg');

    //       $filename2 =$targetPath .round(microtime(true)).'.jpg';
    // imagejpeg($dst, $filename2);
    // imagedestroy($dst);


            $filenamerotate='';
    $targetFolder = '';

    $targetPath = $targetFolder;

  // $filename = $filename;
$degrees = 90;

// Content type
header('Content-type: image/jpeg');

// Load
$source = imagecreatefromjpeg($filename);

// Rotate
  $filenamerotate =$targetPath .round(microtime(true)).'.jpg';
$rotate = imagerotate($source, $degrees, 0);

// Output

  imagejpeg($rotate, $filenamerotate);
// imagejpeg($rotate);

// // Free the memory
// imagedestroy($source);
// imagedestroy($rotate);


// die("dkdkdkdk");

     $src = imagecreatefromjpeg($filenamerotate);
        $dst = imagecreatefromjpeg('demo3.jpg');

        imagecopymerge($dst, $src, 100, 10, 0, 0, 900, 550, 100);
        header('Content-type: image/jpeg');

          $filename2 =$targetPath .round(microtime(true)).'_SHIPJAM'.'.jpg';
    imagejpeg($dst, $filename2);
    imagedestroy($dst);


           $show=$filename2;

          $pdf = PDF::loadView('pdf', compact('show'));

           $pdf->save(public_path($filename2.'.pdf'));


 $show2=$filename2.'.pdf';


                                    $choseorderID = DB::table('label')
                                                    ->insertGetId([
                                                            "IntegratorTxID"=>$rates->IntegratorTxID,
                                                            "TrackingNumber"=>$rates->TrackingNumber,
                                                            "FromZIPCode"=>$dataget->FromZIPCode,   
                                                            "ToZIPCode"=>$dataget->ToZIPCode,
                                                            "Amount"=>$d->total_price,
                                                            "ServiceType"=>$dataget->ServiceType,
                                                            "DeliverDays"=>$dataget->DeliverDays,
                                                            "WeightLb"=>$dataget->WeightLb,
                                                            "WeightOz"=>$dataget->WeightOz,
                                                            "PackageType"=>$dataget->PackageType,
                                                            "ShipDate"=>$dataget->ShipDate,
                                                            "DeliveryDate"=>$dataget->DeliverDays,
                                                            "insuredvalue"=>$demogetcount->price,
                                                            "RectangularShaped"=>"false",
                                                            "FullName"=>$demogetcount->r_name,
                                                            "Address1"=>$demogetcount->r_address_1,
                                                            "City"=>$demogetcount->r_city,    
                                                            "State"=>$demogetcount->r_state,
                                                            "ZIPCode"=>$demogetcount->r_postal_code,
                                                            'Company'=>$demogetcount->r_company,
                                                            "toAddress1"=>$demogetcount->r_address_1,
                                                            "toCity"=>"LOS ANGELES",
                                                            "toState"=>"CA",
                                                            "ZIPCodeAddOn"=>7020,
                                                            "DPB"=>59,
                                                            "CheckDigit"=>6,
                                                            "StampsTxID"=>$rates->StampsTxID,
                                                            "url"=>$url,
                                                            "RateCategory"=>$dataget->RateCategory,
                                                            "email"=>$d->contact_email,
                                                            "img"=>$filename2,
                                                            "orderid"=>$d->id
                                                            // "gateway"=>$demogetcount->gateway,
                                                            // "total_price"=>$demogetcount->total_price,
                                                            // "subtotal_price"=>$demogetcount->subtotal_price,
                                                            // "checkout_id"=>$demogetcount->checkout_id,
                                                            // "currency"=>$demogetcount->currency
                                                            // "productID"=>$prodectid
                                                           ]);    


$files = glob($_SERVER["DOCUMENT_ROOT"]."/".$img);

		// $details='<html> 
  //   <head> 
  //       <title>Welcome to Shipjam</title> 
  //   </head> 
  //   <body>

  //       <h1>Great news, your shipping label is ready. Here your next step.</h1>

  //       <h3>Shipjam recommends shipping within 2 days, the handling option you selected in your listing. </h3>


  //       <table>
  //       <tr><th> Print a shipping Label :- <th> http://phplaravel-529849-1688208.cloudwaysapps.com/'.$img.'<th>
  //       </table>
   
  //       <table cellspacing="0" style="border: 2px dashed #000000; width: 90%; font-size:17px;"> 
  //            <tr> 
  //               <td>http://phplaravel-529849-1688208.cloudwaysapps.com/"'.$img.'</td> 
  //               <td><a href="http://phplaravel-529849-1688208.cloudwaysapps.com/'.$img.'">Link</a></td> 
  //           </tr> 
  //       </table> 
  //   </body>
  //   </html>';

             $details = ' <body style="width:100% !important; margin:0 !important; padding:0 !important; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; background-color:#FFFFFF;">

                   <table bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="25" width="100%">
                        <tr>
                            <td width="100%" bgcolor="#ffffff" style="text-align:left;">
                              <p style="color:#0a1c6b; font-family:Arial, Helvetica, sans-serif; font-size:25px; line-height:19px; margin-top:0; margin-bottom:20px; padding:0; font-weight:normal;">
                                    Great news, your shipping label is ready.                             
                                </p>
                                
                                <p style="color:#0a1c6b; font-family:Arial, Helvetica, sans-serif; font-size:25px; line-height:19px; margin-top:0; margin-bottom:20px; padding:0; font-weight:normal;">
                                       Here\'s your next step.
                                <p>

                   

                                    <table border="0" cellspacing="0" cellpadding="0" width="35%" class="emailwrapto100pc">
                                      <tr>
                                        <td class="emailcolsplit" align="left" valign="top" width="58%"  bgcolor="#ffffff" style="text-align:center;"><a style="font-weight:bold; text-decoration:none;" href="http://phplaravel-529849-1688208.cloudwaysapps.com/public/'.$show2.'"><div style="display:block; max-width:100% !important; width:93% !important; height:auto !important;background-color:#2489B3;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;border-radius:8px;color:#ffffff;font-size:24px;font-family:Arial, Helvetica, sans-serif;">Print a Shipping Label </div></a></td>
                                        </tr>
                                    </table>
                            </td>
                        </tr>
                   </table>
                   <table bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="25" width="100%">
                        <tr>
                            <td width="100%" bgcolor="#ffffff" style="text-align:left;">


                                <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:18px; line-height:19px; margin-top:0; margin-bottom:20px; padding:0; font-weight:normal;">
                                    Shipjam recommends shipping within 2 days, the handling option you selected in your listing.
                                       </p>
                                
                                 <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:18px; line-height:19px; margin-top:0; margin-bottom:20px; padding:0; font-weight:normal;">
                                  You are receiving this email based on your purchase of a shipping label. If you did not place this order, please contact us.
                                 </p>



                          <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:14px; margin-top:0; margin-bottom:15px; padding:0; font-weight:normal;">
                                 OrderID #: ['.$d->id.']
                                </p>


                            <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:14px; margin-top:0; margin-bottom:15px; padding:0; font-weight:normal;">
                                 Order Number #: ['.$d->order_number.']
                                </p>

                              <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:14px; margin-top:0; margin-bottom:15px; padding:0; font-weight:normal;">
                                   We don\'t check this mailbox, so please don\'t reply to this message. If you have a question, please email us at <a style="color:#2489B3; font-weight:bold; text-decoration:underline;" href="mailto:help@shipjam.com">help@shipjam.com.</a> 
                                </p>
                              <p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:14px; margin-top:0; margin-bottom:15px; padding:0; font-weight:normal;">
                                    ©2021 Shipjam.com., 2885 Sanford Ave SW, Ste 14200, Grandville, MI 49418<br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </div>
    </td>
  </tr>
</table> 
</body>'; 


$mail = new PHPMailer(true);

// $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.mailgun.org';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'postmaster@mg.shipjam.com';                     // SMTP username
    $mail->Password   = '20b7cf9a6aa9df4d46b44af1efee32f4-77751bfc-436022f5';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    //Recipients
    $mail->setFrom('postmaster@mg.shipjam.com', 'ShipJam.com');
    $mail->addAddress($d->contact_email);     // Add a recipient
    $mail->addAddress('labels@mg.shipjam.com');
    // $mail->addAddress('tarang027@gmail.com');               // Name is optional
                   // Name is optional
    $mail->addReplyTo('help@shipjam.com', 'Helpshipjam');
    // $mail->addCC('tarang027@gmail.com');
    // $mail->addBCC('surypal@hastishah.com');
    // $mail->addAttachment($files[0]);         // Add attachments
    // $mail->addAttachment('C:/Users/om/Desktop/img/image.jpg', 'new.jpg');    // Optional name
    // dd($mail)

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Your ShipJam.com Shipping Label';
    $mail->Body    = $details;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';


    $mail->send();

    // echo 'Message has been sent';


} catch (Exception $e) {

  $mail = new PHPMailer(true);
// $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.mailgun.org';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'postmaster@mg.shipjam.com';                     // SMTP username
    $mail->Password   = '20b7cf9a6aa9df4d46b44af1efee32f4-77751bfc-436022f5';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    //Recipients
    $mail->setFrom('postmaster@mg.shipjam.com', 'Mailer');
    $mail->addAddress('demotesting0909@gmail.com', 'Testing-mail');     // Add a recipient
    // $mail->addAddress('kalpesh@hastishah.com');
    // $mail->addAddress('tarang027@gmail.com');               // Name is optional
                   // Name is optional
    // $mail->addReplyTo('jigar@omtecweb.com', 'Information');
    // $mail->addCC('tarang027@gmail.com');
    // $mail->addBCC('surypal@hastishah.com');
    // $mail->addAttachment('C:/Users/om/Desktop/img/20210201120215.png');         // Add attachments
    // $mail->addAttachment('C:/Users/om/Desktop/img/image.jpg', 'new.jpg');    // Optional name
    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Shipjam.com';
    $mail->Body    = 'Label Create ';
    $mail->AltBody = 'API IS NOT WROKING ';

    $mail->send();
    // echo 'Message has been sent';


    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
         

     }      



       public function mail2()
    {
        // require 'vendor/autoload.php';
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

// $mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.mailgun.org';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'postmaster@mg.shipjam.com';                     // SMTP username
    $mail->Password   = '20b7cf9a6aa9df4d46b44af1efee32f4-77751bfc-436022f5';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    //Recipients
    $mail->setFrom('postmaster@mg.shipjam.com', 'Mailer');
    $mail->addAddress('demotesting0909@gmail.com', 'Joe User');     // Add a recipient
    // $mail->addAddress('kalpesh@hastishah.com');
    // $mail->addAddress('tarang027@gmail.com');               // Name is optional
                   // Name is optional
    // $mail->addReplyTo('jigar@omtecweb.com', 'Information');
    // $mail->addCC('tarang027@gmail.com');
    // $mail->addBCC('surypal@hastishah.com');
    // $mail->addAttachment('C:/Users/om/Desktop/img/20210201120215.png');         // Add attachments
    // $mail->addAttachment('C:/Users/om/Desktop/img/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Shipjam.com';
    $mail->Body    = 'lABLE create ';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';


} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}






// $mail = new PHPMailer;

// $mail->isSMTP();                                      // Set mailer to use SMTP
// $mail->Host = 'smtp.mailgun.org';                     // Specify main and backup SMTP servers
// $mail->SMTPAuth = true;                               // Enable SMTP authentication
// $mail->Username = 'mg.shipjam.com';   // SMTP username
// $mail->Password = 'f6057e29ce502cc6432e153f87a7cffd-77751bfc-fbc5b15e';                           // SMTP password
// $mail->SMTPSecure = 'tls';                            // Enable encryption, only 'tls' is accepted

// $mail->From = 'shipjam';
// $mail->FromName = 'Mailer';
// $mail->addAddress('bar@example.com');                 // Add a recipient

// $mail->WordWrap = 50;                                 // Set word wrap to 50 characters

// $mail->Subject = 'Hello';
// $mail->Body    = 'Testing some Mailgun awesomness';

// if(!$mail->send()) {
//     echo 'Message could not be sent.';
//     echo 'Mailer Error: ' . $mail->ErrorInfo;
// } else {
//     echo 'Message has been sent';
// }
 }


 function image_resize($file_name, $width, $height, $crop=FALSE) {
   list($wid, $ht) = getimagesize($file_name);
   $r = $wid / $ht;
   if ($crop) {
      if ($wid > $ht) {
         $wid = ceil($wid-($width*abs($r-$width/$height)));
      } else {
         $ht = ceil($ht-($ht*abs($r-$w/$h)));
      }
      $new_width = $width;
      $new_height = $height;
   } else {
      if ($width/$height > $r) {
         $new_width = $height*$r;
         $new_height = $height;
      } else {
         $new_height = $width/$r;
         $new_width = $width;
      }
   }
   $source = imagecreatefromjpeg($file_name);
   $dst = imagecreatetruecolor($new_width, $new_height);
   image_copy_resampled($dst, $source, 0, 0, 0, 0, $new_width, $new_height, $wid, $ht);
   return $dst;
}


function fn_resize($image_resource_id) {
$target_width =200;
$target_height =200;
$target_layer=imagecreatetruecolor($target_width,$target_height);
imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$target_width,$target_height, $width,$height);
return $target_layer;
}
// $img_to_resize = image_resize(‘path-to-jpg-image’, 250, 250);



function Thumbnail($url, $filename, $width = 400, $height = 600) {

 // download and create gd image
 $image = ImageCreateFromString(file_get_contents($url));

 // calculate resized ratio
 // Note: if $height is set to TRUE then we automatically calculate the height based on the ratio
 $height = $height === true ? (ImageSY($image) * $width / ImageSX($image)) : $height;

 // create image 
 $output = ImageCreateTrueColor($width, $height);
 ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));

 // save image
 ImageJPEG($output, $filename, 95);

 // return resized image
 return $output; // if you need to use it

}


}
