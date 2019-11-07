<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Unlu\Laravel\Api\QueryBuilder;
use Illuminate\Support\Facades\Validator;
use App\Permission;
use App\Role;

class PermissionController extends Controller
{
    public function index(Request $request) {
        $queryBuilder = new QueryBuilder(new Permission, $request);
        return response()->json([
            'code' => 0,
            'data' => $queryBuilder->build()->get(),
        ]);
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'name'=>'required|max:40|unique:permissions',
            'guard_name'=>'required',
        ], [
            'name.required' => '2015',
            'name.max' => '2016',
            'name.unique' => '2017',
            'guard_name' => '2018',
        ]);

        if($validator->fails()){
            $validasi = $validator->messages()->toArray();
            // dd($validasi);
            $response = getError('', $validasi);;
            return response()->json($response, 200);
        }

        $name = $request['name'];
        $permission = new Permission();
        $permission->name = $name;
        $permission->guard_name = $request['guard_name'];

        $roles = $request['roles'];

        $permission->save();

        if (!empty($request['roles'])) { // 如果选择了角色
            foreach ($roles as $role) {
                $r = Role::where('id', '=', $role)->firstOrFail(); // 将输入角色和数据库记录进行匹配

                $permission = Permission::where('name', '=', $name)->first(); // 将输入权限与数据库记录进行匹配
                $r->givePermissionTo($permission);
            }
        }
        return response()->json([
            'code' => '0',
            'data' => array(),
        ]);

    }

    public function update(Request $request, $id) {
        $permission = Permission::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name'=>'required|max:40|unique:permissions',
        ], [
            'name.required' => '2015',
            'name.max' => '2016',
            'name.unique' => '2017',
        ]);

        if($validator->fails()){
            $validasi = $validator->messages()->toArray();
            // dd($validasi);
            $response = getError('', $validasi);;
            return response()->json($response, 200);
        }
        $input = $request->all();
        $permission->fill($input)->save();
        return response()->json([
            'code' => '0',
            'data' => array(),
        ]);
    }
}
