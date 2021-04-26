<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use Mail;
use PDF;
use App\Mail\MyDemoMail;
use Mailgun\Mailgun;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Hydrator\NoopHydrator;
use Storage;
use DB;
use Illuminate\Support\Str;

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

        $wsdl       = "https://swsim.testing.stamps.com/swsim/swsimv90.asmx?wsdl";
        $integrationID  = "82db5653-21a9-4355-8067-69beaf2f9e27";
        $username       = "MDP-001";
        $password       = "November2020!";

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

        if($method == "CreateIndicium")
        {

            // print_r($data);
        }

        $result = $this->client->$method($data);

              if($method == "CreateIndicium")
        {

            // print_r($result);
        }

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

            $rates  = $this->GetRates($zipcode, $dzipcode, null,$weight,$length,$width,$height,$packagetype, $shipDate,$insuredvalue,null);

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
            // dd($request->all(),"THIS IS MULTI DATA");


    	$ControlTotal=0;
    	$TransactionID=0;
        require_once '../shopifydata/functions.php';
        $collects=array();
           $var = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/customers.json','GET',$collects);

            $collects = json_decode($var['response'], true);


              // dd($var);

    	
 //    					while ( 1 == 1) {
	// while ( 1 == 1) {

 //    							$getaccountinfo=$this->getaccountinfo();
 //    			$ControlTotal=$getaccountinfo->AccountInfo->PostageBalance->ControlTotal;
    								 
 //    					if(!empty($ControlTotal))
 //    						{
 //    							break;
 //    						}
 //    					 }
 //                            $getPurchasePostage=$this->getPurchasePostage($ControlTotal);
 //                // dd($getPurchasePostage);
 //                    $TransactionID=$getPurchasePostage->TransactionID;

 //                        if((!empty($TransactionID) && $TransactionID != 0))
 //                            {
 //                                break;
 //                            }
 //                         }


// =================== Insert Product in Shopify ==============================

            $sprocess= Str::random(10);
  $data='{
                 "product": {
                        "title": "Omtec-'.$sprocess.'",
                        "body_html": "THIS label Product ",
                        "vendor": "omtec",
                        "product_type": "Shipjam_omtec",
                        "shop": {
                          "id": 690933842,
                          "name": "Omtec-'.$sprocess.'",
                          "email": "'.$request->r_email.'",
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


                    // dd($sprocess ,$data);


                 $collects = json_decode($data, true);
              

            $var = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/products.json','POST',$collects);

          $prodectinsert = json_decode($var['response']);

                      $variant='{
                                    "variant": {
                                    "option1": "Item1'.$request->price.'",
                                    "price": "'.$request->price.'"
                                    }
                                  }';

     $collectsvariant = json_decode($variant, true);  


        dd($prodectinsert,$collectsvariant);


           $collectsvariantshow = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/products/'.$prodectinsert ->product->id.'/variants.json','POST',$collectsvariant);

             $p= json_decode($collectsvariantshow['response']);

           $getproduct = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/products/'.$prodectinsert ->product->id.'.json','GET',$query = array());

$getproduct_show= json_decode($getproduct['response']);


           $locationsget = shopify_call('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','ship-jam.myshopify.com','/admin/api/2021-01/locations.json','GET',$query = array());

                            $plocationsget= json_decode($locationsget['response']);


                                    $variant='{
                                            "location_id": '.$plocationsget->locations[0]->id.',
                                          "inventory_item_id": '.$getproduct_show->product->variants[1]->inventory_item_id.',
                                          "available_adjustment": 10
                                         }';

     $collectsvariant = json_decode($variant, true);  


           $collectsvariantshow = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/inventory_levels/adjust.json','POST',$collectsvariant);


             $p= json_decode($collectsvariantshow['response']);


                    $productid=json_encode($prodectinsert ->product->id);
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
                                              'r_email'=>$request->r_email,
                                              'r_url'=>$request->r_url,
                                              'courier_id'=>$request->courier_id,
                                              'price'=>$request->price,
                                              'productid' =>$productid,
                                              'inventory_item_id'=>$inventoryitemid,
                                              'variantsid' =>$variantsid,
                                                  ]);


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



    // public function orderprocess()
    public function orderprocess(Request $request)
    {

                     // $demoget=json_encode($request->all());
                      
                // DB::table('orderp')
                //         ->insertGetId([
                //                         'order'=>$demoget
                //                     ]);

                //         dd("djdhdhddhdh");

                $demoget= DB::table('orderp')->where('id',1)->first();

                          $p= json_decode($demoget->order);
                 // dd($demoget ,$p );

                  // $demoget2= DB::table('orderp')->where('id',2)->first();

                  // dd($demoget2);


                           // $p2= json_decode($demoget2->order);
                           //  // $demoget= json_decode($demoget);

                           //  dd($demoget ,$p ,$p2);

                                $getaccountinfo=$this->getaccountinfo();

                                $ControlTotal=$getaccountinfo->AccountInfo->PostageBalance->ControlTotal;
                                 
                                
                        $getPurchasePostage=$this->getPurchasePostage($ControlTotal);
                // dd($getPurchasePostage);
                    $TransactionID=$getPurchasePostage->TransactionID;

             $shipDate = date('Y-m-d');

                   $st= Str::random(4);

             $data = [
            "Authenticator" => $this->authenticator,
            "IntegratorTxID"=>"82db5653d".$st."-21a9-4355-8067-69beaf2f9e27",
            "Rate"=>[
                    "FromZIPCode"=>$request->zipcode,
                    "ToZIPCode"=>$request->dzipcode,
                    // "Amount"=>$request->price,
                    "ServiceType"=>"US-PM",
                    "DeliverDays"=>"1-1",
                    "WeightLb"=>$request->weight,
                    "WeightOz"=>$request->weight,
                    "PackageType"=>$request->packagetype,
                    "ShipDate"=>"2021-01-06",
                    "InsuredValue"=>$request->price,
                    "RectangularShaped"=>false
                  ],
             "From"=>[
                    "FullName"=>$request->r_name,
                    "Address1"=>$request->r_address_1,
                    "City"=>$request->r_city,    
                    "State"=>$request->r_state,
                    "ZIPCode"=>$request->r_postal_code
                    ],
              "To"=>[
                    "FullName"=>$request->r_name,  
                    "NamePrefix"=>"GEOFF",
                    'Company'=>$request->r_company,
                    "Address1"=>$request->r_address_2,
                    "City"=>"LOS ANGELES",
                    "State"=>"CA",
                    "ZIPCode"=>$request->dzipcode,
                    "ZIPCodeAddOn"=>"7020",
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

$urlinsert = $url ;  


  $iname=date("Ymdhis");
$img = 'asset/imges/'.$iname.'.png';

// Save image
$ch = curl_init($url);
$fp = fopen($img, 'wb');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);
curl_close($ch);
fclose($fp);


   $choseorderID = DB::table('label')
                            ->insertGetId([
                                    "IntegratorTxID"=>$rates->IntegratorTxID,
                                    "TrackingNumber"=>$rates->TrackingNumber,
                                    "FromZIPCode"=>$dataget->FromZIPCode,   
                                    "ToZIPCode"=>$dataget->ToZIPCode,
                                    "Amount"=>$dataget->Amount,
                                    "ServiceType"=>$dataget->ServiceType,
                                    "DeliverDays"=>$dataget->DeliverDays,
                                    "WeightLb"=>$dataget->WeightLb,
                                    "WeightOz"=>$dataget->WeightOz,
                                    "PackageType"=>$dataget->PackageType,
                                    "ShipDate"=>$dataget->ShipDate,
                                    "DeliveryDate"=>$dataget->DeliveryDate,
                                    "insuredvalue"=>$request->price,
                                    "RectangularShaped"=>"false",
                                    "FullName"=>$request->r_name,
                                    "Address1"=>$request->r_address_1,
                                    "City"=>$request->r_city,    
                                    "State"=>$request->r_state,
                                    "ZIPCode"=>$request->r_postal_code,
                                    'Company'=>$request->r_company,
                                    "toAddress1"=>$request->r_address_1,
                                    "toCity"=>"LOS ANGELES",
                                    "toState"=>"CA",
                                    "ZIPCodeAddOn"=>7020,
                                    "DPB"=>59,
                                    "CheckDigit"=>6,
                                    "StampsTxID"=>$rates->StampsTxID,
                                    "url"=>$url,
                                    "RateCategory"=>$dataget->RateCategory,
                                    "email"=>$request->r_email,
                                    "img"=>$img
                                   ]);              


    }



}
