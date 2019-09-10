<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Mongo\Muser;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
class UserController extends Controller
{
    /** 用户列表
     *
     *
     * @param request
     *
     * @return array
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

    /** 用户登录
     *
     *
     * @param request
     *
     * @return item
     */
    public function authenticate(Request $request)
    {
        $data =json_decode($request->getContent(), true);
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
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
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                $code = 404;
                $response =[ 'code'=>$code,'message'=>'Email atau Password yang anda masukan Salah'];
                return response()->json($response, $code);
            }
        } catch (JWTException $e) {
            $response = ['status'=>respon_error()];
            return response()->json($response, 404);
        }
        $last_login =   Carbon::now();

 		$data = User::where('email',$request->email)->first();
        $data->last_login = $last_login;
        $data->save();
        $user_detail = Muser::where('_id',$data->_iduser)->first();
        $users = [
            'id' => $data->id,
            'email' => $data->email,
            'first_name' => $data->first_name,
            'token' => 'Bearer '.$token,
            'last_login'=> $data->last_login,
            'user_details'=>$user_detail
        ];

        return response()->json([
            'code'=>200,'message'=>'Call successfully',
            'contents'=> $users,
        ],200);

    }
    /** 登录验证
     *
     *
     * @param validasi
     *
     * @return error
     */
    public function  validation_message($validasi){

        $error = array();
        foreach ($validasi as $key => $value) {
            $error[$key]=$value[0];
        }
        return $error;
    }

    /** 用户注册
     *
     *
     * @param request
     *
     * @return item
     */
    public function store(Request $request) {

    }

    /** 获取登录的用户信息
     *
     *
     *
     * @return item
     */
    public function getAuthenticatedUser()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_detail = Muser::where('_id',$user->_iduser)->first();
        $users = [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_login'=> $user->last_login,
            'user_details'=>$user_detail
        ];
        $response = ['code'=>200,'message'=>'success','contents'=>$users];
        return response()->json($response,200);
    }

    /** 退出登录
     *
     *
     * @param request
     *
     * @return
     */
    public function destroy( Request $request )
    {
        $logout = JWTAuth::invalidate();
        $response = ['code'=>200,'message'=>'success logout'];
        return response()->json($response, 200);
    }

}
