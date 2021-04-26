<?php

namespace App\Http\Controllers\API;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use OhMyBrew\BasicShopifyAPI;
use OhMyBrew\ShopifyApp\Models\Shop;
use RocketCode\Shopify\API;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use SoapClient;


class HelperController extends Controller
{

    public $shopify;
    public $shop;

    public function getShopify(){
        $shop = ShopifyApp::shop();
        $this->shopify = App::make('ShopifyAPI', [
            'API_KEY' => env('SHOPIFY_API_KEY'),
            'API_SECRET' => env('SHOPIFY_API_SECRET'),
            'SHOP_DOMAIN' => $shop->shop_name,
            'ACCESS_TOKEN' => $shop->shopify_token
        ]);
        return $this->shopify;
    }
    public function BasicShopifyDomain($domain){
        $this->basic_api = new BasicShopifyAPI();
        $this->basic_api->setVersion('2020-01'); // "YYYY-MM" or "unstable"
        $this->basic_api->setShop($domain);
        $this->basic_api->setAccessToken($this->getShopDomain($domain)->shopify_token);
        $this->basic_api->setApiSecret(env('SHOPIFY_API_SECRET'));
        $this->basic_api->setApiKey(env('SHOPIFY_API_KEY'));
        return $this->basic_api;
    }

    public function getShopifyDomain($domain)
    {
        $shop = Shop::where('shop_name', $domain)->first();
        $this->shopify = App::make('ShopifyAPI', [
            'API_KEY' => env('SHOPIFY_API_KEY'),
            'API_SECRET' => env('SHOPIFY_API_SECRET'),
            'SHOP_DOMAIN' => $shop->shop_name,
            'ACCESS_TOKEN' => $shop->shopify_token
        ]);
        return $this->shopify;
    }

    public function getShop()
    {
        // $shop = ShopifyApp::shop();
//        $shop = Shop::where('shop_name', env('SHOPIFY_WEB_URL'))->first();
        $this->shop = Shop::where('shop_name', $shop->shop_name)->first();
        return $this->shop;
    }

    public function getShopDomain($domain)
    {
        $this->shop = Shop::where('shop_name', $domain)->first();
        return $this->shop;
    }

    public function DeleteAllOrders()
    {
        $orders = $this->getShopify()->call([
            'METHOD' => 'GET',
            'URL' => '/admin/api/2019-04/orders.json',
        ]);
        foreach ($orders->orders as $order) {
            $this->getShopify()->call([
                'METHOD' => 'DELETE',
                'URL' => '/admin/api/2019-04/orders/' . $order->id . '.json',
            ]);
        }
    }
    public function themes()
    {
        $themes = $this->getShopify()->call([
            'METHOD' => 'GET',
            'URL' => '/admin/api/2020-01/themes.json',
        ]);
        return view('theme')->with([
            'themes' => $themes->themes
        ]);
    }

    public function themeSave(Request $request)
    {
       
        $path = 'https://phpstack-387016-1524069.cloudwaysapps.com/scripts.txt';
        $content = file_get_contents($path);
        if ($request->theme_id) {
            $theme_id = $request->theme_id;
        } else {
            $themes = $this->getShopify()->call([
                'METHOD' => 'GET',
                'URL' => '/admin/api/2020-01/themes.json',
            ]);
            foreach ($themes->themes as $theme) {
                if ($theme->role == 'main') {
                    $theme_id = $theme->id;
                }
            }
        }
        $asset_not_find = true;
        $assets = $this->getShopify()->call([
            'METHOD' => 'GET',
            'URL' => '/admin/api/2020-01/themes/' . $theme_id . '/assets.json',
        ]);
        $assets = $assets->assets;
        foreach ($assets as $asset) {
            if ($asset->key == 'snippets/leagueaccs.liquid') {
                $asset_not_find = false;
            }
        }
        if ($asset_not_find) {
            $create_snippet_file = $this->getShopify()->call([
                'METHOD' => 'PUT',
                'URL' => '/admin/api/2020-01/themes/' . $theme_id . '/assets.json',
                'DATA' => [
                    "asset" => [
                        "key" => "snippets/leagueaccs.liquid",
                        "value" => $content
                    ]
                ]
            ]);
        }
        $asset = $this->getShopify()->call([
            'METHOD' => 'GET',
            'URL' => '/admin/api/2020-01/themes/' . $theme_id . '/assets.json?asset[key]=layout/theme.liquid&theme_id=' . $theme_id,
        ]);
        $theme = $asset->asset->value;
        if (stripos($theme, "leagueaccs") == true) {
        } else {
            $head = explode('</body>', $theme);
            $content = $head[0] . "\n {% include 'leagueaccs' %} \n </body>" . $head[1];
            $update_theme_file = $this->getShopify()->call([
                'METHOD' => 'PUT',
                'URL' => '/admin/api/2020-01/themes/' . $theme_id . '/assets.json',
                'DATA' => [
                    "asset" => [
                        "key" => "layout/theme.liquid",
                        "value" => $content
                    ]
                ]
            ]);
        }

        $shop = Shop::where('id', $this->getShop()->id)->first();
        if ($shop) {
            return redirect()->back()->with('success', 'Installed Successfully');
//            return response()
//                ->json(['SHOPIFY_TOKEN' => $shop->shopify_token, 'API_KEY' => env('SHOPIFY_API_KEY'), 'API_SECRET' => env('SHOPIFY_API_SECRET')]);
        } else {
//            return response()
//                ->json([null]);
            return redirect()->back()->with('danger', 'Something Went Wrong');
        }
    }


}
