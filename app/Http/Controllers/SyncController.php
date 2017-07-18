<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App;
use DB;
class SyncController extends Controller
{
    public function sync() {
        //make functions to deal with each shop, then normalize data into an inventory array
        // if item exists in array we deal with changes in quantity
        // finailly we loop through the array and either insert of update items in database
        $inventory = array();
        $shopifyProducts = $this->gatherShopify();

        foreach($shopifyProducts as $products){
            foreach($products->variants as $variant){
                $inventory[$variant->sku] = array($products->title, $variant->sku, $variant->price, $variant->inventory_quantity);
            }
        }

        foreach($inventory as $item){
            $record = DB::table('inventory')->where('sku', "=", $item[1])->get();
            if ($record->isEmpty()) {
                DB::table('inventory')->insert(['product' => $item[0], 'sku' => $item[1], 'price' => $item[2], 'quantity' => $item[3]]);
            }
            else{
                DB::table('inventory')->where('sku', '=', $item[1])->update(['product' => $item[0], 'sku' => $item[1], 'price' => $item[2], 'quantity' => $item[3]]);
            }
        }
        
        return \Response::json(['status' => 'success'],201);
    }


    public function gatherShopify(){
    $shopify = App::make('ShopifyAPI'); 
    $shopify->setup([ 
        'API_KEY' => getenv('SHOP_API_KEY'), 
        'API_SECRET' => getenv('SHOP_API_SECRET'), 
        'SHOP_DOMAIN' => getenv('SHOP_DOMAIN'), 
        'ACCESS_TOKEN' => getenv('SHOP_ACCESS_TOKEN')
    ]);
    // Gets a list of products 
    try
    {
    $call = $shopify->call(['URL' => 'products.json', 'METHOD' => 'GET', 'DATA' => ['limit' => 5, 'published_status' => 'any', 'fields' => 'title,variants']]);
    }
    catch (Exception $e)
    {
        $call = $e->getMessage();
    }
    $products = $call->products;
    return $products;
    }

}