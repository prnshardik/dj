<?php    
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use App\Models\User;
    use App\Models\Item;
    use App\Models\ItemInventory;
    use App\Models\SubItem;
    use App\Models\SubItemInventory;

    use Auth, Validator, DB, Mail, DataTables, File;

    class PrintsController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $search = $request->search;
                    $option = $request->option;
                    $selected = json_decode($request->selected);

                    if($option == 'items'){
                        $collection = Item::select('id', 'name');

                        if($search != '')
                            $collection->where('name', 'like', '%'.$search.'%');
                    }elseif($option == 'subItems'){
                        $collection = SubItem::select('id', 'name');

                        if($search != '')
                            $collection->where('name', 'like', '%'.$search.'%');
                    }elseif($option == 'itemsInventories'){
                        $collection = ItemInventory::select('id', 'title as name');

                        if($search != '')
                            $collection->where('title', 'like', '%'.$search.'%');
                    }elseif($option == 'subItemsInventories'){
                        $collection = SubItemInventory::select('id', 'title as name');

                        if($search != '')
                            $collection->where('title', 'like', '%'.$search.'%');
                    }

                    $data = $collection->paginate(100);
                    
                    $view = view('prints.table', compact('data', 'selected'))->render();
                    $pagination = view('prints.pagination', compact('data'))->render();
                    
                    return response()->json(['success' => true, 'data' => $view, 'pagination' => $pagination]);

                }

                return view('prints.index');
            }
        /** index */     
        
        /** print */
            public function print(Request $request){
                $formOption = $request->formOption;
                $formId = $request->formId;
                $formHeight = $request->formHeight;
                $formWidth = $request->formWidth;
                $formQuantity = $request->formQuantity;

                $ids = [];
                $collection = [];
                $count = count($formHeight);

                foreach($formId as $k => $v){
                    array_push($ids, $formId[$k]);
                    $collection[$k] = ['height' => $formHeight[$k], 'width' => $formWidth[$k], 'quantity' => $formQuantity[$k]];
                }

                $data = collect();

                if($formOption == 'items'){
                    foreach($formId as $k => $v){
                        _generate_qrcode($v, 'item');
                    }

                    $data = Item::select('qrcode', 'name')->whereIn('id', $ids)->get();
                    
                    if($data->isNotEmpty()){
                        foreach($data as $k => $v){
                            $v->height = $collection[$k]['height'];
                            $v->width = $collection[$k]['width'];
                            $v->quantity = $collection[$k]['quantity'];
                        }
                    }
                }elseif($formOption == 'subItems'){
                    foreach($formId as $k => $v){
                        _generate_qrcode($v, 'sub_item');
                    }

                    $data = SubItem::select('qrcode', 'name')->whereIn('id', $ids)->get();
                    
                    if($data->isNotEmpty()){
                        foreach($data as $k => $v){
                            $v->height = $collection[$k]['height'];
                            $v->width = $collection[$k]['width'];
                            $v->quantity = $collection[$k]['quantity'];
                        }
                    }
                }elseif($formOption == 'itemsInventories'){
                    foreach($formId as $k => $v){
                        _generate_qrcode($v, 'item_inventory');
                    }

                    $data = ItemInventory::select('qrcode', 'title as name')->whereIn('id', $ids)->get();
                    
                    if($data->isNotEmpty()){
                        foreach($data as $k => $v){
                            $v->height = $collection[$k]['height'];
                            $v->width = $collection[$k]['width'];
                            $v->quantity = $collection[$k]['quantity'];
                        }
                    }
                }elseif($formOption == 'subItemsInventories'){
                    foreach($formId as $k => $v){
                        _generate_qrcode($v, 'sub_item_inventory');
                    }

                    $data = SubItemInventory::select('qrcode', 'title as name')->whereIn('id', $ids)->get();
                    
                    if($data->isNotEmpty()){
                        foreach($data as $k => $v){
                            $v->height = $collection[$k]['height'];
                            $v->width = $collection[$k]['width'];
                            $v->quantity = $collection[$k]['quantity'];
                        }
                    }
                }

                return view('prints.print', ['data' => $data, 'option' => $formOption]);
            }
        /** print */
    }