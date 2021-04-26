<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use Mail;
use PDF;
use App\Mail\MyDemoMail;

use Storage;
class ApiController extends Controller
{

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

// $wsdl       = "https://swsim.testing.stamps.com/swsim/swsimv90.asmx?wsdl";
// $integrationID  = "c929ea59-dd49-4329-bd50-ab2d772e5f77";
// $username       = "MDepot-001";
// $password       = "May2020!";


    public function __construct()
    {
$wsdl       = "https://swsim.testing.stamps.com/swsim/swsimv90.asmx?wsdl";
$integrationID  = "c929ea59-dd49-4329-bd50-ab2d772e5f77";
$username       = "MDepot-001";
$password       = "May2020!";
        // $this->client = new SoapClient($wsdl);

        $authData = [
            "Credentials"   => [
                "IntegrationID"     => $integrationID,
                "Username"          => $username,
                "Password"          => $password
            ]
        ];

        // $this->makeCall('AuthenticateUser', $authData);
        // $this->account = $this->makeCall('GetAccountInfo', ["Authenticator" => $this->authenticator]);
    }



    protected function makeCall($method, $data) {

        $result = $this->client->$method($data);
        $this->authenticator = $result->Authenticator;
        // dd($result); 
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

        $rates = $this->makeCall('getRates', $data)->Rates->Rate;

        foreach ($rates as $k => $v) {
            foreach ($data['Rate'] as $kk => $vv) {
                $result[$k][$kk] = $v->$kk;
            }

            $result[$k] =  $result[$k] + array(
                "ServiceType" => $this->ServiceType[$v->ServiceType],
                "Amount" => $v->Amount,
                "PackageType" => $v->PackageType,
                "WeightLb" => $v->WeightLb,
                "Length" => $v->Length,
                "Width" => $v->Width,
                "Height" => $v->Height,
                "ShipDate" => $v->ShipDate,
                "DeliveryDate" => property_exists($v, 'DeliveryDate') ? $v->DeliveryDate : 'Unavailable',
                "RateCategory" => $v->RateCategory,
                "ToState" => $v->ToState,
            );
        }
        // $this->label();
    return $result;
    }
private $wsdl       = "https://swsim.testing.stamps.com/swsim/swsimv90.asmx?wsdl";
private  $integrationID  = "c929ea59-dd49-4329-bd50-ab2d772e5f77";
private $username       = "MDepot-001";
private $password       = "May2020!";



public function reateget(Request $request)
{
// dd("JDJDJDJD");

	// dd($request->all());
// $shipDate   = date('Y-m-d');

	$rates  = $this->GetRates($request->FromZIPCode, $request->FromZIPCode, null,$request->WeightLb,$request->Length,$request->Width,$request->Height,$request->PackageType, $request->ShipDate,$request->InsuredValue, null);

	dd($rates);

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
           dd($rates);
            echo '<pre>';
            print_r("====== GET getaccountinfo ===============");
            print_r($rates);

       return $rates;
    }




    public function GetPurchaseStatus(Request $request)
    {

         $data = [
            "Authenticator" => $this->authenticator,
            "TransactionID"  => $request->TransactionID, 
            // 72492724,
            // "ControlTotal" => $controlTotal
        ];

           $rates = $this->makeCall('GetPurchaseStatus', $data);
           dd($rates);
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
            // "PurchaseAmount"  => $request->purchaseamount, 
            // "ControlTotal" => $request->controltotal
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


    public function createindicium(Request $request)
    {

            // dd($request->all(),"zjjfjfjfj");

             $data = [
            "Authenticator" => $this->authenticator,
            "IntegratorTxID"=>$request->IntegratorTxID,
            "Rate"=>[
                    "FromZIPCode"=>$request->FromZIPCode,
                    "ToZIPCode"=>$request->ToZIPCode,
                    "Amount"=>$request->Amount,
                    "ServiceType"=>$request->ServiceType,
                    "DeliverDays"=>$request->DeliverDays,
                    "WeightLb"=>$request->WeightLb,
                    "WeightOz"=>$request->WeightOz,
                    "PackageType"=>$request->PackageType,
                    "ShipDate"=>$request->ShipDate,
                    "InsuredValue"=>$request->InsuredValue,
                    "RectangularShaped"=>$request->RectangularShaped
                  ],
             "From"=>[
                    "FullName"=>$request->FullName,
                    "Address1"=>$request->Address1,
                    "City"=>$request->City,    
                    "State"=>$request->State,
                    "ZIPCode"=>$request->ZIPCode
                    ],
              "To"=>[
                    "FullName"=>$request->toFullName,  
                    "NamePrefix"=>$request->NamePrefix,
                    "FirstName"=>$request->FirstName,    
                    "MiddleName"=>$request->MiddleName,
                    "LastName"=>$request->LastName,
                    "NameSuffix"=>$request->NameSuffix,
                    "Title"=>$request->Title,
                    "Department"=>$request->Department,
                    "Company"=>$request->Company,
                    "Address1"=>$request->toAddress1,
                    "Address2"=>$request->Address2,
                    "City"=>$request->toCity,
                    "State"=>$request->toState,
                    "ZIPCode"=>$request->toZIPCode,
                    "ZIPCodeAddOn"=>$request->ZIPCodeAddOn,
                    "DPB"=>59,
                    "CheckDigit"=>6,
                    "Province"=>"",
                    "PostalCode"=>"",
                    "Country"=>"",
                    "Urbanization"=>"",
                    "PhoneNumber"=>"",
                    "Extension"=>"",
                    "CleanseHash"=>"7SWYAzuNh82cWhIQyRFXRNa71HFkZWFkYmVlZg==20070513"
                    ]         

            ];


  $rates = $this->makeCall('createindicium', $data);

// $date = date("Ymdhis");

// dd($date);

  $url = $rates->URL;

// Image path
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


$info = pathinfo($rates->URL);
$contents = file_get_contents($url);
$file = 'asset/imges/' . $info['basename'];
file_put_contents($file, $contents);
$uploaded_file = new UploadedFile($file, $info['basename']);

// date("h:i:sa")
  // dd($rates->URL);

  // $myEmail = 'demotesting0909@gmail.com';
  //       Mail::to($myEmail)->send(new MyDemoMail());
// dd($img);
  // $data = array('name'=>"Jigar Modi LARAVEL EMAIL SEND"); 
  //     Mail::send('emails.myDemoMail', $data, function($message) {
  //        $message->to('demotesting0909@gmail.com', 'label attachment')->subject 
  //           ('Laravel Testing Mail with Attachment');
  //        $message->attach('asset\imges\20200910082042.png'); 
  //        // $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
  //        $message->from('demotesting0909@gmail.com','Jigar MOdi');
  //     });
  //     dd("Email Sent with attachment. Check your inbox.");


 $data["email"]="demotesting0909@gmail.com";
        $data["client_name"]="JIGAR MODI";
        $data["subject"]="DEMO";
        $pdf = PDF::loadView('emails.myDemoMail', $data);

        mail($to, $subject, $message, $headers);
        try{
            Mail::send('emails.myDemoMail', $data, function($message)use($data,$pdf) {
            $message->to($data["email"], $data["client_name"])
            ->subject($data["subject"])
            ->attachData($pdf->output(), "invoice.pdf");
            });
        }catch(JWTException $exception){
            $this->serverstatuscode = "0";
            $this->serverstatusdes = $exception->getMessage();
        }
        if (Mail::failures()) {
             $this->statusdesc  =   "Error sending mail";
             $this->statuscode  =   "0";

        }else{

           $this->statusdesc  =   "Message sent Succesfully";
           $this->statuscode  =   "1";
        }
        return response()->json(compact('this'));

            echo '<pre>';
         print_r("====== GET createindicium ===============");
         print_r($rates);
        return $rates;

    } 



    public function pdf()
    {

        $data = ['title' => 'Welcome to ItSolutionStuff.com',
                  'img'=>'prod5.jpg' 
                    ];
        $pdf = PDF::loadView('emails.myDemoMail', $data);

        return $pdf->download('Label.pdf');

            $to  = 'jigar@omtecweb.com';
            $subject = 'the subject';
            $message = $pdf;
            $headers = 'From: no-reply@omtecweb.com' . "\r\n" .
                'Reply-To: no-reply@omtecweb.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);

        dd($pdf);

        // dd("HDHDHD");

 //          $myEmail = 'demotesting0909@gmail.com';
 //        Mail::to($myEmail)->send(new MyDemoMail());


    }



    public function sendmail()
    {
        $to  = 'jigar@omtecweb.com';
$subject = 'the subject';
$message = 'hello HSHSHSHSHSHSH';
$headers = 'From: no-reply@omtecweb.com' . "\r\n" .
    'Reply-To: no-reply@omtecweb.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);

   $data = ['title' => 'Welcome to ItSolutionStuff.com',
                  'img'=>'prod5.jpg' 
                    ];
        $pdf = PDF::loadView('myPDF', $data);

                $to  = 'jigar@omtecweb.com';
$subject = 'the subject';
$message = $pdf;
$headers = 'From: no-reply@omtecweb.com' . "\r\n" .
    'Reply-To: no-reply@omtecweb.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
    }


    public function CancelIndicium(Request $request)
    {
            // dd("HDHDHD");
          $data = [
            "Authenticator" => $this->authenticator,
            "StampsTxID" =>"c605aec1-322e-48d5-bf81-b0bb820f9c22"
        ];
                // dd($request->all() ,$data);
           $rates = $this->makeCall('CancelIndicium', $data);
           dd($rates);

    }
    //
}
