<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
   

	public function Demotesting()
	{
		    	// dd("THIS IS JIGAR MODI");
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://swsim.testing.stamps.com/swsim/swsimv90.asmx",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => 
  '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"> 
  <soap:Body>
    <AuthenticateUser xmlns="http://stamps.com/xml/namespace/2019/12/swsim/SwsimV90">
      <Credentials>
        <IntegrationID>c929ea59-dd49-4329-bd50-ab2d772e5f77</IntegrationID>
        <Username>MDepot-001</Username>
        <Password>May2020!</Password>
      </Credentials>
    </AuthenticateUser>
  </soap:Body>
</soap:Envelope>',
  CURLOPT_HTTPHEADER => array( "SOAPAction:http://stamps.com/xml/namespace/2019/12/swsim/SwsimV90/AuthenticateUser", "Content-Type: text/xml"),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  // dd("JDJDDJJDJD");
	DD("cURL Error #:",$err);
  // echo "cURL Error #:" . $err;
} else {
	dd($response);
  // echo $response;

  // $fileContents = str_replace(array("\n", "\r", "\t"), '', $response);
}
		 return response()->json("HELLEOEOEOE");
	}




  public function Demotesting22()
  {

     return response()->json("JIGAR MDODODKsasasasDOMD");

  }


}
