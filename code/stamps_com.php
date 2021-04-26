<?php

class stamps_com
{
    private $authenticator;
    private $client;
    private $account;

    private $ServiceType = array(
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

    public function __construct  ($wsdl, $integrationID, $username, $password)
    {
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

    private function makeCall($method, $data) {
        $result = $this->client->$method($data);
        $this->authenticator = $result->Authenticator;
        return $result;
    }

    public function GetRates($FromZIPCode, $ToZIPCode = null, $ToCountry = null, $WeightLb, $Length, $Width, $Height, $PackageType, $ShipDate, $InsuredValue, $ToState = null)
    {
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


     public function GetAccountInfo()
    {
        $data = [
            "Authenticator" => $this->authenticator
            
        ];


           $rates = $this->makeCall('GetAccountInfo', $data);

       return $rates;
    }



    public function GetPurchaseStatus()
    {

         $data = [
            "Authenticator" => $this->authenticator,
            "TransactionID"  =>72492724
           
        ];

           $rates = $this->makeCall('GetPurchaseStatus', $data);

        return $rates;


    }


    // public function PurchasePostage()
    // {

    //      $data = [
    //         "Authenticator" => $this->authenticator,
    //         "PurchaseAmount"  =>"100",
    //         "ControlTotal" => "500.00"
    //     ];

    //         $rates = $this->makeCall('PurchasePostage', $data);

    //     return $rates;

    // }



}

$wsdl           = "https://swsim.testing.stamps.com/swsim/swsimv90.asmx?wsdl";
$integrationID  = "c929ea59-dd49-4329-bd50-ab2d772e5f77";
$username       = "MDepot-001";
$password       = "May2020!";

$stamps_com = new stamps_com($wsdl, $integrationID, $username, $password);

$shipDate   = date('Y-m-d');
$rates      = $stamps_com->GetRates("90210", "90210", null, "10", 6, 6, 6, "Package", $shipDate, '100', null);
$GetAccountInfo = $stamps_com->GetAccountInfo();
$GetPurchaseStatus = $stamps_com->GetPurchaseStatus();




echo '<pre>';
print_r("====== GET rates ===============");
print_r($rates);
print_r("====GET GetAccountInfo========");
print_r($GetAccountInfo);
print_r("====GET INFO GetPurchaseStatus========");
print_r($GetPurchaseStatus);
print_r("====GET INFO PurchasePostage========");

// print_r($PurchasePostage);

