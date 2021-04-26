<?php

namespace App\Http\Controllers;
use Oseintow\Shopify\Facades\Shopify;
use Illuminate\Http\Request;
use mysql_xdevapi\Session;
use App\Shop;

class ShopController extends Controller
{


    public function index(){
        return view('pages.shop');
    }
    public function shop(Request $request)
    {
        if ($_GET["shop"]) {
            $shopUrl = $_GET["shop"];
      $scope = ["read_orders", "read_products","write_products","write_draft_orders","read_draft_orders","read_orders","write_orders","read_checkouts","write_checkouts","read_customers","write_customers","read_translations","write_translations","read_inventory","write_inventory","read_script_tags","write_script_tags","read_price_rules","write_price_rules"];
            // $redirectUrl = 'https://laraveldemo.estorewhiz.com/laravel_shipjam/auth';
            $redirectUrl = 'https://phplaravel-529849-1688208.cloudwaysapps.com/auth';

           
            $shopify = Shopify::setShopUrl($shopUrl);
            return redirect()->to($shopify->getAuthorizeUrl($scope, $redirectUrl));
        } else {
            return 'Please enter shop url';
        }
    }
    public function authenticate(Request $request){
            

        $shopUrl = $request->input('shop');
        $accessToken = Shopify::setShopUrl($shopUrl)->getAccessToken($request->code);

            dd($accessToken);
            // dd($request->all() ,$accessToken);
        // if (Shop::where('shop_name', '=', $shopUrl)->exists()) {
        //     $shop = Shop::where('shop_name', $shopUrl)
        //         ->update([
        //             'access_token'=>$accessToken
        //         ]);
        // }else{
        //     $shop = Shop::create([
        //         'shop_name'=> $shopUrl,
        //         'access_token'=>$accessToken
        //     ]);
        // }
        session(['shop_name' => $shopUrl]);
        session(['access_token' => $accessToken]);

        return view('pages.index');
    }


    public function getprodect()
    {

        require_once './shopifydata/functions.php';

           $var = shopify_call('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','ship-jam.myshopify.com','/admin/api/2020-10/products.json','GET',$query = array());
             $collects = json_decode($var['response'], true);
                dd($var , $collects);


        

                    $data='{
                      "product": {
                        "title": "123456_Demo",
                        "body_html": "<strong>Good snowboard!</strong>",
                        "vendor": "Burton",
                        "product_type": "Snowboard",
                        "shop": {
                          "id": 690933842,
                          "name": "Apple Computers",
                          "email": "steve@apple.com",
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
                 // dd($collects);
            $var = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/products.json','POST',$collects);

                    $prodectinsert= json_decode($var['response']);


                      dd($prodectinsert);


                      $variant='{
                        "variant": {
                          "option1": "THS",
                          "price": "50.00"
                        }
                      }';

     $collectsvariant = json_decode($variant, true);  


           $var = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/products/6266270646441/variants.json','POST',$collectsvariant);

                    $prodectinsertvariant= json_decode($var['response']);

                dd($prodectinsertvariant);
// POST /admin/api/2020-10/products/632910392/variants.json


            $collects = json_decode($var['response']['product'], true);

                $img="/admin/api/2020-10/products/".$collects['product']['id']."/images.json";

                dd($var , $collects['product']['id'],$img);

            $data='{
              "image": {
                "src": "https://transitioncorner.com/wp-content/uploads/2020/12/resume-analysis.png"
              }
            }';


        $collects = json_decode($data, true);
                
            $var = shopify_call('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','jigar-omtec.myshopify.com',$img,'POST',$collects);

            dd($var,$collects);
            //dd($var);

         
    }


    public function customerget()
    {

        require_once './shopifydata/functions.php';

        $collects=array();

          // ======Get all customers Api================

           $var = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/customers.json','GET',$collects);
            $collects = json_decode($var['response'], true);

          // ========= End Get all customers Api================


            // ======Get Single customers Data Get Api================

             $var1 = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/customers/4584841281705.json','GET',$collects);
            $collects1 = json_decode($var1['response'], true);

            // ======End Get Single customers Data Get Api================


           // dd($collects1);


            // ======Get customers Count Number Get Api================

            $var2 = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/customers/count.json','GET',$collects);

            // ====== End Get customers Count Number Get Api================

              // ====== Get checkouts Get Api================

            $var3 = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/checkouts.json','GET',$collects);

              // ======End  Get checkouts Get Api================


              // ======Get  All products Get Api================


                 $var4 = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/products.json','GET',$query = array());
             $collects4 = json_decode($var4['response'], true);

              // ====== End Get All products Get Api================
             
              // ====== Get All checkouts Get Api================

             $var5 = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/checkouts.json','GET',$query = array());
             $collects5 = json_decode($var5['response'], true);

                                    $checkout ='{
                                                  "checkout": {
                                                    "line_items": [
                                                      
                                                        "variant_id": 38050949300393,
                                                        "quantity": 2
                                                      
                                                    ]
                                                  }
                                                }';

                          $collects = json_decode($checkout, true);

        // ====== End Get All checkouts Get Api================
                                // dd($collects);    

                    //       $varin = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/checkouts.json','POST',$collects);

                    // $prodectinsert= json_decode($varin['response']);

          $varin = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/checkouts/755863feb71af4613bb728c6b88e1e94/complete.json','POST',$collects);

                    $prodectinsert= json_decode($varin['response']);

              $var5 = shopify_call('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','jigar-omtec.myshopify.com','/admin/api/2020-10/checkouts/755863feb71af4613bb728c6b88e1e94.json','GET',$query = array());
             $collects5 = json_decode($var5['response'], true);
           
          
                // dd($collects,$collects1 ,$var2 ,$var3,$collects4 ,$var5,$collects5['checkouts'][0]['token'],$varin,$prodectinsert);


              dd($collects,$collects1 ,$var2 ,$var3,$collects4 ,$var5,$collects5,$varin,$prodectinsert);

    } 


    public function thankspage($url)
    {
            dd($url);
    }

    public function thankspagedemo(Request $request)
    {


          return $request->id;
    }






}
