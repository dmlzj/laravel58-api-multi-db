<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Admin;
use App\Mongo\Muser;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
use Unlu\Laravel\Api\RequestCreator;
use Unlu\Laravel\Api\QueryBuilder;

class UserController extends Controller
{
    /**
     *用户列表
     * @SWG\Get(
     *     path="/api/v1/admin/users",
     *     description="用户列表",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         type="string",
     *         description="",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Data area."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Unauthorized action.",
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Token Exipred",
     *     )
     * )
     */

    public function index(Request $request)
    {
        $queryBuilder = new QueryBuilder(new User, $request);

        return response()->json([
            'code' => 0,
            'data' => $queryBuilder->build()->get(),
        ]);
    }

    /** 用户详情
     *
     *
     * @param request
     *
     * @return item
     */
    public function show(Request $request) {
        $req = RequestCreator::createWithParameters([
            'id' => $request->id,
        ]);
        $queryBuilder = new QueryBuilder(new User, $req);
        $data = $queryBuilder->build()->get();
        if (count($data) > 0) {
            return response()->json([
                'code' => 0,
                'data' => $data[0],
            ]);
        } else {
            return response()->json([
                'code' => 2001,
                'data' => '用户不存在',
            ]);
        }
        // $user->find()
    }

    // 前端用户登录
    public function userLogin(Request $request) {
        return $this->authenticate($request, 'user');
    }

    // 后端用户登录
    public function adminLogin(Request $request) {
        return $this->authenticate($request, 'admin');
    }

    /** 用户登录
     *
     *
     * @param request
     *
     * @return item
     */
    public function authenticate(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email',
            'mobile' => 'sometimes|required|phone',
            'username' => 'sometimes|required',
            'password' => 'required',
        ], [
            'email.required' => '2001',
            'email.email' => '2002',
            'mobile.required' => '2003',
            'mobile.phone' => '2004',
            'username.required' => '2014',
            'password.required' => '2005',
        ]);


        if($validator->fails()){
            $status['success']= false;
            $status['code'] =404;
            $status['message'] ='Error validation';
            $validasi = $validator->messages()->toArray();
            $error = $this->validation_message($validasi);
            $response = compact('status','error');
            return response()->json($response, 400);
        }
        if ($request->input('email')) {
            $credentials = $request->only('email', 'password');
        } else if ($request->input('mobile')) {
            $credentials = $request->only('mobile', 'password');
        } else if ($request->input('username')) {
            $credentials = $request->only('username', 'password');
        } else {
            $error = getError('2000');
            return response()->json($error, 200);
        }
        return $this->login($credentials, $request->input('password'), $type);

    }


    /**
     *注册
     * @SWG\Post(
     *     path="/api/v1/users/regist",
     *     description="注册",
     *     produces={"application/json"},
     *     tags={"users"},
     *     @SWG\Parameter(
     *         name="mobile",
     *         in="formData",
     *         type="string",
     *         description="",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         type="string",
     *         description="",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="sms_code",
     *         in="formData",
     *         type="string",
     *         description="",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         type="string",
     *         description="",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="注册返回",
     *     @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="id", type="int",example=1,description="用户id"),
     *            @SWG\Property(property="mobile", type="string",example="13361567903",description="手机号"),
     *            @SWG\Property(property="email", type="string",example="super@admin.com",description="邮箱"),
     *            @SWG\Property(property="nickname", type="string",example="test",description="昵称"),
     *            @SWG\Property(property="username", type="string",example="dmlzj",description="用户名"),
     *            @SWG\Property(property="token", type="string",example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9zeS1hcGkuY29tXC9hcGlcL3YxXC91c2Vyc1wvcmVnaXN0IiwiaWF0IjoxNTY4MTg4MTc1LCJleHAiOjE1NjkzOTc3NzUsIm5iZiI6MTU2ODE4ODE3NSwianRpIjoiRWcwOXprQXJ6bGxOOTJtbiIsInN1YiI6OSwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9.T764OQnRkDZY3pv-g2xHgNorcu1TMlwa8tgqfdQQokQ",description="登录成功令牌"),
     *     	)
     *     ),
     * )
     */

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email|unique:user',
            'mobile' => 'sometimes|required|phone|unique:user',
            'sms_code' => 'required_with:mobile|max:6',
            'password' => 'required|alpha_dash',
        ], [
            'email.required' => '2001',
            'email.email' => '2002',
            'email.unique' => '2013',
            'mobile.required' => '2003',
            'mobile.phone' => '2004',
            'mobile.unique' => '2012',
            'sms_code.required_with' => '2006',
            'sms_code.max' => '2007',
            'password.required' => '2005',
            'password.alpha_dash' => '2011'
        ]);

        if($validator->fails()){
            $validasi = $validator->messages()->toArray();
            // dd($validasi);
            $response = getError('', $validasi);;
            return response()->json($response, 200);
        }
        $data = [
            'password' => bcrypt($request->input('password'))
        ];
        // dd($data);
        $res = true;
        if(!is_null($request->input('mobile'))) {
            // 验证验证码
            $data['mobile'] = $request->input('mobile');
            $res = User::where('mobile', $data['mobile']);
            // dd($res);
            if ($res->count() === 0) {
                return $this->saveUser($data, $request->password);
            } else {
                $error =getError('2008');;
                return response()->json($error, 200);
            }
        } else if (!is_null($request->input('email'))) {
            // 邮箱数据
            $data['email'] = $request->input('email');
            $res = User::where('email', $data['email']);
            if ($res->count() === 0) {
                return $this->saveUser($data, $request->password);
            } else {
                $error = getError('2008');;
                return response()->json($error, 200);
            }
        } else {
            $error = getError('2010');;
            return response()->json($error, 200);
        }

    }
    // 保存用户信息
    private function saveUser(array $data, $pwd) {

        $res = User::create($data);
        return $this->login($data, $pwd);

    }
    // 用户登录
    private function login($credentials, $pwd, $type) {
        // dd($credentials);
        if ($type === 'user') {
            \Config::set( 'auth.defaults.guard','api');

        } else {
            \Config::set( 'auth.defaults.guard','admin');
        }
        $credentials['password'] = $pwd;
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                $response = getError('2009');
                return response()->json($response, 200);
            }
        } catch (JWTException $e) {
            $response = ['status'=>respon_error()];
            return response()->json($response, 200);
        }
        $last_login = Carbon::now();
        $data = null;
        if (isset($credentials['email'])) {
            $data = User::where('email', $credentials['email'])->first();
        } else if (isset($credentials['username'])) {
            if ($type === 'user') {
                $data = User::where('username', $credentials['username'])->first();
            } else {
                $data = Admin::where('username', $credentials['username'])->first();
            }
        } else {
            $data = User::where('mobile', $credentials['mobile'])->first();
        }

        $data->last_login = $last_login;
        $data->save();

        if ($type == 'admin') {
            $users = [
                'id' => $data->id,
                'username' => $data->username,
                'avatar' => $data->avatar,
                'roles' => ['admin'],
                'token' => 'Bearer '.$token,
                // 'user_details'=>$user_detail
            ];
        } else {
            $users = [
                'id' => $data->id,
                'email' => $data->email,
                'mobile' => $data->mobile,
                'username' => $data->username,
                'nickname' => $data->nickname,
                'token' => 'Bearer '.$token,
                // 'user_details'=>$user_detail
            ];
        }

        return response()->json([
            'code'=>'0',
            'data'=> $users,
        ],200);
    }
    // 返回后台用户信息
    public function adminInfo() {
        return $this->getAuthenticatedUser('admin');
    }
    // 返回前台用户信息
    public function userInfo() {
        return $this->getAuthenticatedUser('user');
    }
    /** 获取登录的用户信息
     *
     *
     *
     * @return item
     */
    public function getAuthenticatedUser($type)
    {
        $users = [];
        if ($type === 'user') {
            \Config::set( 'auth.defaults.guard','api');
            $user = JWTAuth::parseToken()->authenticate();
            $users = [
                'id' => $user->id,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'username' => $user->username,
                'nickname' => $user->nickname,
                'last_login'=> $user->last_login,
            ];
        } else {
            \Config::set( 'auth.defaults.guard','admin');
            $user = JWTAuth::parseToken()->authenticate();
            $users = [
                'id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'roles' => ['admin'],
                'last_login'=> $user->last_login,
            ];
        }

        $response = ['code'=> '0','data'=>$users];
        return response()->json($response,200);
    }

    // 普通用户退出登录
    public function userLogout(Request $request) {
        return $this->destroy($request, 'user');
    }

    // 后台用户退出登录
    public function adminLogout(Request $request) {
        return $this->destroy($request, 'admin');
    }

    /** 退出登录
     *
     *
     * @param request
     *
     * @return
     */
    public function destroy( Request $request, $type )
    {
        if ($type === 'user') {
            \Config::set( 'auth.defaults.guard','api');
        } else {
            \Config::set( 'auth.defaults.guard','admin');
        }
        JWTAuth::getToken();
        $logout = JWTAuth::invalidate();
        $response = ['code'=> '0','data'=>'success logout'];
        return response()->json($response, 200);
    }

}
