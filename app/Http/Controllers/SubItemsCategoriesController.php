<?php    
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use App\Models\SubItemCategory;
    use App\Http\Requests\SubItemCategoryRequest;
    use Auth, Validator, DB, Mail, DataTables, File;

    class SubItemsCategoriesController extends Controller{
        /** index */
            public function index(Request $request){
                if($request->ajax()){
                    $data = SubItemCategory::select('id', 'title', DB::Raw("SUBSTRING(".'description'.", 1, 30) as description"), 'status')->orderBy('id','desc')->get();

                    return Datatables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($data){
                                return ' <div class="btn-group btn-sm">
                                                <a href="'.route('sub.items.categories.view', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a> 
                                                <a href="'.route('sub.items.categories.edit', ['id' => base64_encode($data->id)]).'" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a>  
                                                <a href="javascript:;" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa fa-bars"></i>
                                                </a> 
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="active" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Active</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="inactive" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Inactive</a></li>
                                                    <li><a class="dropdown-item" href="javascript:;" onclick="change_status(this);" data-status="deleted" data-old_status="'.$data->status.'" data-id="'.base64_encode($data->id).'">Delete</a></li>
                                                </ul>
                                            </div>';
                            })

                            ->editColumn('status', function($data) {
                                if($data->status == 'active')
                                    return '<span class="badge badge-pill badge-success">Active</span>';
                                else if($data->status == 'inactive')
                                    return '<span class="badge badge-pill badge-warning">Inactive</span>';
                                else if($data->status == 'deleted')
                                    return '<span class="badge badge-pill badge-danger">Delete</span>';
                                else
                                    return '-';
                            })

                            ->rawColumns(['action', 'status'])
                            ->make(true);
                }
                return view('sub-items.categories.index');
            }
        /** index */

        /** create */
            public function create(Request $request){
                return view('sub-items.categories.create');
            }
        /** create */

        /** insert */
            public function insert(SubItemCategoryRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $crud = [
                        'title' => ucfirst($request->title),
                        'description' => $request->description ?? NULL,
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => auth()->user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $last_id = SubItemCategory::insertGetId($crud);
                    
                    if($last_id)
                        return redirect()->route('sub.items.categories')->with('success', 'Record added successfully');
                    else
                        return redirect()->back()->with('error', 'Faild to add record')->withInput();
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** insert */

        /** view */
            public function view(Request $request, $id=''){
                if($id == '')
                    return redirect()->back()->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = SubItemCategory::select('id', 'title', 'description')->where(['id' => $id])->first();
                
                if($data)
                    return view('sub-items.categories.view', ['data' => $data]);
                else
                    return redirect()->back()->with('error', 'No record found');
            }
        /** view */

        /** edit */
            public function edit(Request $request, $id=''){
                if($id == '')
                    return redirect()->back()->with('error', 'Something went wrong');

                $id = base64_decode($id);

                $data = SubItemCategory::select('id', 'title', 'description')->where(['id' => $id])->first();

                if($data)
                    return view('sub-items.categories.edit', ['data' => $data]);
                else
                    return redirect()->back()->with('error', 'No record found');
            }
        /** edit */ 

        /** update */
            public function update(SubItemCategoryRequest $request){
                if($request->ajax()){ return true; }

                if(!empty($request->all())){
                    $exst_record = SubItemCategory::where(['id' => $request->id])->first(); 

                    $crud = [
                        'title' => ucfirst($request->title),
                        'description' => $request->description ?? NULL,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => auth()->user()->id
                    ];

                    $update = SubItemCategory::where(['id' => $request->id])->update($crud);

                    if($update)
                        return redirect()->route('sub.items.categories')->with('success', 'Record updated successfully');
                    else
                        return redirect()->back()->with('error', 'Faild to update record')->withInput();
                }else{
                    return redirect()->back()->with('error', 'Something went wrong')->withInput();
                }
            }
        /** update */

        /** change-status */
            public function change_status(Request $request){
                if(!$request->ajax()){ exit('No direct script access allowed'); }

                if(!empty($request->all())){
                    $id = base64_decode($request->id);
                    $status = $request->status;

                    $data = SubItemCategory::where(['id' => $id])->first();

                    if(!empty($data)){
                        if($status == 'deleted')
                            $update = SubItemCategory::where(['id' => $id])->delete();
                        else
                            $update = SubItemCategory::where(['id' => $id])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
                        
                        if($update)
                            return response()->json(['code' => 200]);
                        else
                            return response()->json(['code' => 201]);
                    }else{
                        return response()->json(['code' => 201]);
                    }
                }else{
                    return response()->json(['code' => 201]);
                }
            }
        /** change-status */
    }