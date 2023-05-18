<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Roles';
	    return view('admin.roles.index',compact('title'));
    }
     /**
     * get users for ajax request especially
     *
     * @return \Illuminate\Http\Response
     */
    public function getRoles(Request $request){
		$columns = array(
			0 => 'id',
			1 => 'name',
			2 => 'created_at',
			3 => 'action'
		);

		$totalData = Role::count();
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		if(empty($request->input('search.value'))){
			$users = Role::offset($start)
				->limit($limit)
				->orderBy($order,$dir)
				->get();
			$totalFiltered = Role::count();
		}else{
			$search = $request->input('search.value');
			$users = Role::where([
				['name', 'like', "%{$search}%"],
			])
				->orWhere('created_at','like',"%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();
			$totalFiltered = Role::where([
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
				$edit_url = route('roles.edit',$r->id);
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
        $title = 'Add New Role';
        $permissions=Permission::all();
	    return view('admin.roles.create',compact('title','permissions'));
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
            'permission' => 'required',
	    ]);

	    $input = $request->all();
	    $role = new Role();
	    $role->name = $input['name'];
	    $role->save();
        $role->syncPermissions($request->input('permission'));
	    Session::flash('success_message', 'Great! Role has been saved successfully!');
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

    public function roleDetail(Request $request)
	{
		$user = Role::findOrFail($request->id);
		return view('admin.roles.detail', ['title' => 'Role Detail', 'role' => $user]);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title="Edit Role";
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
	    return view('admin.roles.edit',compact('role','permission','rolePermissions','title'));
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
	    $role = Role::find($id);
	    $this->validate($request, [
		    'name' => 'required|max:255',
            'permission' => 'required',
	    ]);
	    $input = $request->all();

	    $role->name = $input['name'];
	    $role->save();
        $role->syncPermissions($request->input('permission'));
	    Session::flash('success_message', 'Great! Role successfully updated!');
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
        $role = Role::find($id);
	    if($role->is_admin == 0){
		    $role->delete();
		    Session::flash('success_message', 'Role successfully deleted!');
	    }
	    return redirect()->route('roles.index');
    }
    public function deleteSelectedRoles(Request $request)
	{
		$input = $request->all();
		$this->validate($request, [
			'roles' => 'required',

		]);
		foreach ($input['users'] as $index => $id) {
			$role = Role::find($id);
            $role->delete();
		}
		Session::flash('success_message', 'Roles successfully deleted!');
		return redirect()->back();

	}
}
