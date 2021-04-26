<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use Mail;
use PDF;
use App\Mail\MyDemoMail;
use Storage;
use DB;
use App\User;
use ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Auth;
class HomeController extends Controller
{

    public function loginpage()
    {
         return view('loginpage');
    }



    public function logincheck(Request $request)
    {


                         $credentials = [
                                'email' => $request->email,
                                'password' =>$request->password,
                            ];
                                                   if (Auth::attempt($credentials)) 
                                                   {
                                                        $labeldata = DB::table('label')->orderBy('id', 'desc')->get();

                                                        // dd(" THIS IS HOME PAGE ",$labeldata);
                                                      return redirect()->route('dashboard'); 
                                                          
                                                   }
                                                   else
                                                   {
                                                          return view('loginpage');
                                                   }  
             
    }





		public function homepage()
		{

				$labeldata = DB::table('label')->orderBy('id', 'desc')->get();

			// dd(" THIS IS HOME PAGE ",$labeldata);
			 return view('welcome1')->withlabel($labeldata);
		}



        public function detailslist()
        {

                    $details = DB::table('details')->orderBy('id', 'desc')->get();

                     return view('details')->withlabel($details);
        }

              public function detailsadd()
        {

            
                     return view('detailsadd');
        }


        public function detailsinsert(Request $request)
        {




                    // dd($request->all());


            $request->validate([
            'min'=>'required',
            'max'=>'required',
            'is_percentage'=>'required',
            'amount'=>'required',

        ],[
            'min.required'=>'This field is required.',
            'max.required'=>'This field is required.',
            'is_percentage.required'=>'This field is required.',
            'amount.required'=>'This field is required.',
        ]);

               $ID= Auth::user()->id;
            // dd($request->all(),$data);

                 if($request->editroleID == null){


                      $data =   DB::table('details')
                         ->insertGetId([
                                'min'=>$request->min,
                                'max'=>$request->max,
                                'is_percentage'=>$request->is_percentage,
                                'amount'=>$request->amount,
                                'created_id'=>$ID
                                ]);
                     }
                     else
                     {
                         $demo=DB::table("details")
                    ->where("id",$request->editroleID)  
                    ->update([
                            'min'=>$request->min,
                                'max'=>$request->max,
                                'is_percentage'=>$request->is_percentage,
                                'amount'=>$request->amount,
                                'created_id'=>$ID
                                ]);

                     }

                     return redirect()->route('detailslist'); 
            
        }


        public function detailsedit($id)
        {
            $editpage = DB::table('details')->where('id',$id)->first();
               return view('detailsadd')->witheditpage($editpage);
        }



        public function detailsdelete($id)
        {
             $editpage = DB::table('details')->where('id',$id)->delete();

              return redirect()->route('detailslist'); 

        }


		public function deletelabel(Request $request)
		{




					// dd($request->all());

			   $noIsert = array();
        $curl = curl_init();
     
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://laraveldemo.estorewhiz.com/laravel_shipjam/api/CancelIndicium/".$request->id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 90000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
            ) ,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err)
        {
            echo "cURL Error #:" . $err;
        	return "No";
        }
        else
        {

        			$allda = json_decode($response, true);

        				if($allda['data'] == 1)
        				{
        					return "Yes";
        				}
        				else
        				{
        					return "No";
        				}
        		// dd($allda['data'],$response,"DDJDJDJDJ");

        }





	}


    public function mail()
    {

        
            $message= "Hi, I am from OMTEC.com from mailgun testing.";
         $user = User::find(1)->toArray();

            // dd($user['email']);


    Mail::send('emails.mailEvent', $user, function($message) use ($user) {
        $message->to($user['email']);
        $message->subject('Mailgun Testing');
    });
    dd('Mail Send Successfully');


        dd("ddhdhdhd");
    }




      public function logout(Request $request)
     {
          Auth::logout();

         return redirect()->route('homepage');
     }







}
