<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]); //to access index and show function atleat one of mentioned permission should be assigned
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Users';
	    return view('admin.users.index',compact('title'));
    }
    /**
     * get users for ajax request especially
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsers(Request $request){
		$columns = array(
			0 => 'id',
			1 => 'name',
			2 => 'email',
			3 => 'active',
			4 => 'created_at',
			5 => 'action'
		);

		$totalData = User::count();
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		if(empty($request->input('search.value'))){
			$users = User::offset($start)
				->limit($limit)
				->orderBy($order,$dir)
				->get();
			$totalFiltered = User::count();
		}else{
			$search = $request->input('search.value');
			$users = User::where([
				['name', 'like', "%{$search}%"],
			])
				->orWhere('email','like',"%{$search}%")
				->orWhere('created_at','like',"%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();
			$totalFiltered = User::where([
				['name', 'like', "%{$search}%"],
			])
				->orWhere('name', 'like', "%{$search}%")
				->orWhere('email','like',"%{$search}%")
				->orWhere('created_at','like',"%{$search}%")
				->count();
		}


		$data = array();

		if($users){
			foreach($users as $r){
				$edit_url = route('users.edit',$r->id);
				$nestedData['id'] = '<td><div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input" type="checkbox" name="users[]" value="'.$r->id.'"></div></td>';
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
                                    <a class="btn btn-icon btn-sm btn-primary btn-outline" onclick="event.preventDefault();viewInfo('.$r->id.');" title="View User" href="javascript:void(0)">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a title="Edit User" class="btn btn-icon btn-sm btn-success"
                                       href="'.$edit_url.'">
                                       <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a class="btn btn-icon btn-sm btn-danger" onclick="event.preventDefault();del('.$r->id.');" title="Delete User" href="javascript:void(0)">
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
        $title = 'Add New User';
	    return view('admin.users.create',compact('title'));
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
		    'email' => 'required|unique:users,email',
		    'password' => 'required|min:6',
	    ]);

	    $input = $request->all();
	    $user = new User();
	    $user->name = $input['name'];
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
                $user->image = $image;

            }
        }
	    $user->email = $input['email'];
	    $res = array_key_exists('active', $input);
	    if ($res == false) {
		    $user->active = 0;
	    } else {
		    $user->active = 1;

	    }
	    $user->password = bcrypt($input['password']);
	    $user->save();

	    Session::flash('success_message', 'Great! User has been saved successfully!');
	    $user->save();
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

    public function userDetail(Request $request)
	{
		$user = User::findOrFail($request->id);
		return view('admin.users.detail', ['title' => 'User Detail', 'user' => $user]);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
	    return view('admin.users.edit', ['title' => 'Edit User details'])->withUser($user);
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
	    $user = User::find($id);
	    $this->validate($request, [
		    'name' => 'required|max:255',
		    'email' => 'required|unique:users,email,'.$user->id,
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
                $user->image = $image;

            }
        }
	    $user->name = $input['name'];
	    $user->email = $input['email'];
	    $res = array_key_exists('active', $input);
	    if ($res == false) {
		    $user->active = 0;
	    } else {
		    $user->active = 1;

	    }
	    if(!empty($input['password'])) {
		    $user->password = bcrypt($input['password']);
	    }

	    $user->save();

	    Session::flash('success_message', 'Great! User successfully updated!');
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
	    $user = User::find($id);
	    if($user->is_admin == 0){
		    $user->delete();
		    Session::flash('success_message', 'User successfully deleted!');
	    }
	    return redirect()->route('users.index');

    }

    public function deleteSelectedUsers(Request $request)
	{
		$input = $request->all();
		$this->validate($request, [
			'users' => 'required',

		]);
		foreach ($input['users'] as $index => $id) {
			$user = User::find($id);
            $user->delete();
		}
		Session::flash('success_message', 'Users successfully deleted!');
		return redirect()->back();

	}
}
