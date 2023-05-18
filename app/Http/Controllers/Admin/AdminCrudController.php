<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class AdminCrudController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:admin-list|admin-create|admin-edit|admin-delete', ['only' => ['index','show']]);
        $this->middleware('permission:admin-create', ['only' => ['create','store']]);
        $this->middleware('permission:admin-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:admin-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Admins';
	    return view('admin.admins.index',compact('title'));
    }

    public function getAdmins(Request $request){
		$columns = array(
			0 => 'id',
			1 => 'name',
            2=>'email',
			3 => 'created_at',
			4 => 'action'
		);

		$totalData = Admin::count();
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		if(empty($request->input('search.value'))){
			$admins = Admin::offset($start)
				->limit($limit)
				->orderBy($order,$dir)
				->get();
			$totalFiltered = Admin::count();
		}else{
			$search = $request->input('search.value');
			$admins = Admin::where([
				['name', 'like', "%{$search}%"],
			])
				->orWhere('email','like',"%{$search}%")
				->orWhere('created_at','like',"%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();
			$totalFiltered = Admin::where([
				['name', 'like', "%{$search}%"],
			])
				->orWhere('name', 'like', "%{$search}%")
				->orWhere('email','like',"%{$search}%")
				->orWhere('created_at','like',"%{$search}%")
				->count();
		}


		$data = array();

		if($admins){
			foreach($admins as $r){
				$edit_url = route('admins.edit',$r->id);
				$nestedData['id'] = '<td><div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input" type="checkbox" name="admins[]" value="'.$r->id.'"></div></td>';
				$nestedData['name'] = $r->name;
				$nestedData['email'] = $r->email;
				if($r->active){
					$nestedData['active'] = '<span class="badge py-3 px-4 fs-7 badge-light-success">Active</span>';
				}else{
					$nestedData['active'] = '<span class="badge py-3 px-4 fs-7 badge-light-danger">Inactive</span>';
				}

				$nestedData['created_at'] = date('d-m-Y',strtotime($r->created_at));
				$nestedData['action'] = '
                                <div>
                                <td>
                                    <a class="btn btn-icon btn-sm btn-primary btn-outline" onclick="event.preventDefault();viewInfo('.$r->id.');" title="View Admin" href="javascript:void(0)">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a title="Edit Admin" class="btn btn-icon btn-sm btn-success"
                                       href="'.$edit_url.'">
                                       <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a class="btn btn-icon btn-sm btn-danger" onclick="event.preventDefault();del('.$r->id.');" title="Delete Admin" href="javascript:void(0)">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                                </div>
                            ';
				$data[] = $nestedData;
			}
		}

		$json_data = array(
			"draw"			=> intval($request->input('draw')),
			"recordsTotal"	=> intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"			=> $data
		);

		echo json_encode($json_data);

	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Add New Admin';
        $roles = Role::pluck('name','name')->all();
	    return view('admin.admins.create',compact('title','roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $this->validate($request, [
		    'name' => 'required|max:255',
            'email' => 'required|email|unique:admins,email',
            'password'=>'required|min:8',
            'roles'=>'required'
	    ]);

	    $input = $request->all();
	    $admin = new Admin();
	    $admin->name = $input['name'];
        $admin->password = $input['password'];
        $admin->email = $input['email'];
        $res = array_key_exists('active', $input);
	    if ($res == false) {
		    $admin->active = 0;
	    } else {
		    $admin->active = 1;

	    }
	    $admin->save();
        $admin->assignRole($request->input('roles'));
	    Session::flash('success_message', 'Great! Admin has been saved successfully!');
	    return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function adminDetail(Request $request)
	{
		$admin = Admin::findOrFail($request->id);
		return view('admin.admins.detail', ['title' => 'Admin Detail']);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin=Admin::findOrFail($id);
        $title="Edit Admin";
        $roles = Role::pluck('name','name')->all();
        $adminRole = $admin->roles->pluck('name','name')->all();
        return view('admin.admins.edit',compact('roles','adminRole','title','admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
	    $admin = Admin::find($id);
	    $this->validate($request, [
		    'name' => 'required|max:255',
		    'email' => 'required|unique:admins,email,'.$admin->id,
	    ]);
	    $input = $request->all();
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $this->validate($request, [
                    'image' => 'required|image|mimes:jpeg,png,jpg'
                ]);
                $file = $request->file('image');
                $destinationPath = public_path('/uploads');
                //$extension = $file->getUserOriginalExtension('logo');
                $image = $file->getUserOriginalName('image');
                $image = rand().$image;
                $request->file('image')->move($destinationPath, $image);
                $admin->image = $image;

            }
        }
	    $admin->name = $input['name'];
	    $admin->email = $input['email'];
	    $res = array_key_exists('active', $input);
	    if ($res == false) {
		    $admin->active = 0;
	    } else {
		    $admin->active = 1;

	    }
	    if(!empty($input['password'])) {
		    $admin->password = bcrypt($input['password']);
	    }

	    $admin->save();
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $admin->assignRole($request->input('roles'));
	    Session::flash('success_message', 'Great! Admin successfully updated!');
	    return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
	    $admin = Admin::find($id);
	    if($admin->is_admin == 0){
		    $admin->delete();
		    Session::flash('success_message', 'Admin successfully deleted!');
	    }
	    return redirect()->route('admins.index');

    }

    public function deleteSelectedAdmins(Request $request)
	{
		$input = $request->all();
		$this->validate($request, [
			'admins' => 'required',

		]);
		foreach ($input['admins'] as $index => $id) {
			$admin = Admin::find($id);
            $admin->delete();
		}
		Session::flash('success_message', 'admins successfully deleted!');
		return redirect()->back();

	}
}
