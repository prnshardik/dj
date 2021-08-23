<?php    

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\User;
    use App\Models\Item;
    use App\Models\ItemCategory;
    use App\Models\ItemInventory;
    use App\Models\SubItem;
    use App\Models\SubItemCategory;
    use App\Models\SubItemInventory;
    use App\Models\Cart;
    use App\Models\CartInventory;
    use App\Models\CartSubInventory;
    use Auth, DB;

    class DashboardController extends Controller{

        /** index */
            public function index(Request $request){
                $users = User::where(['status' => 'active', 'is_admin' => 'n'])->count();

                $itemsCategories = ItemCategory::where(['status' => 'active'])->count();
                $items = Item::where(['status' => 'active'])->count();
                $itemsInventories = ItemInventory::where(['status' => 'active'])->count();

                $subItemsCategories = SubItemCategory::where(['status' => 'active'])->count();
                $subItems = SubItem::where(['status' => 'active'])->count();
                $subItemsInventories = SubItemInventory::where(['status' => 'active'])->count();

                $cart = Cart::where('status', '!=', 'reach')->count();

                $itemsRepairings = Item::select('id', 'name', DB::Raw("'items' as type"))->where(['status' => 'repairing'])->limit(5)->get();
                $subItemsRepairings = subItem::select('id', 'name', DB::Raw("'sub items' as type"))->where(['status' => 'repairing'])->limit(5)->get();

                $repairings = $itemsRepairings->merge($subItemsRepairings);

                $carts = Cart::select('cart.id', 'u.name as user_name', 'cart.party_name', 'cart.party_address', 'cart.status')
                                    ->leftjoin('users as u', 'u.id', 'cart.user_id')
                                    ->whereNotIn('cart.status', ['reach', 'assigned'])
                                    ->limit(5)
                                    ->get();

                if($carts->isNotEmpty()){
                    foreach($carts as $row){
                        $cart_item = CartInventory::select(DB::Raw("COUNT(".'id'.") as count"))->where(['cart_id' => $row->id])->first();
                        $row->items = $cart_item->count;
                    }
                }

                if($carts->isNotEmpty()){
                    foreach($carts as $row){
                        $cart_sub_item = CartSubInventory::select(DB::Raw("COUNT(".'id'.") as count"))->where(['cart_id' => $row->id])->first();
                        $row->sub_items = $cart_sub_item->count;
                    }
                }

                $data = [
                        'users' => $users, 
                        'itemsCategories' => $itemsCategories, 
                        'items' => $items, 
                        'itemsInventories' => $itemsInventories,
                        'subItemsCategories' => $subItemsCategories, 
                        'subItems' => $subItems, 
                        'subItemsInventories' => $subItemsInventories,
                        'cart' => $cart, 
                        'repairings' => $repairings,
                        'carts' => $carts
                    ];

                return view('dashboard', ['data' => $data]);
            }
        /** index */
    }