<?php    
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use App\Models\User;
    use App\Models\Log;
    use App\Models\Cart;
    use App\Models\Item;
    use App\Models\SubItem;
    use Auth, Validator, DB, Mail, DataTables, File;

    class LogController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = Log::select('log.id', 'log.user_id', 'log.item_id', 'log.item_type', 'log.type', 'log.status', 'log.type',
                                            'users.name as user_name'
                                        )
                                    ->leftjoin('users', 'users.id', 'log.user_id')
                                    ->orderBy('id','desc')
                                    ->get();

                    if($data->isNotEmpty()){
                        foreach($data as $row){
                            $item = 'Cart';
                            if($row->item_type == 'items'){
                                $itemData = Item::select('name')->where(['id' => $row->item_id])->first();

                                if(!empty($itemData))
                                    $item = $itemData->name;
                            }elseif($row->item_type == 'sub_items'){
                                $itemData = SubItem::select('name')->where(['id' => $row->item_id])->first();

                                if(!empty($itemData))
                                    $item = $itemData->name;
                            }

                            $row->item_type = str_replace('_', ' ', $row->item_type);
                            $row->logs = "$item ($row->item_type) has been $row->status for $row->type by $row->user_name";
                        }
                    }

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->make(true);
                }

                return view('logs.index');
            }
        /** index */        
    }