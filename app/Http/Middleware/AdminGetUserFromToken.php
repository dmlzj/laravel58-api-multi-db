<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AdminGetUserFromToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        \Config::set( 'auth.defaults.guard','admin');
        $code = 401;
        $status = array('success'=>false,'code'=>$code,'message'=>'token not found');
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            $response = ['code'=>$code,'message'=>'Authorization not provided'];
            return response()->json($response, $code);
        }
        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            // $message ='Authorization expired';
            // $code;
            // $response = ['code'=>$code,'message'=>$message];
            // return response()->json($response, $code);

            // 此处捕获到了 token 过期所抛出的 TokenExpiredException 异常，我们在这里需要做的是刷新该用户的 token 并将它添加到响应头中
            try {
                // 刷新用户的 token
                // $token = $this->auth->refresh();
                $token = $this->auth->parseToken()->refresh();
                // 使用一次性登录以保证此次请求的成功
                Auth::guard('api')->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                // 在响应头中返回新的 token
                return $this->setAuthenticationHeader($next($request), $token);
            } catch (JWTException $exception) {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                // throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
                $message ='Authorization invalid';
                $response = ['code'=>$code,'message'=>$message];
                return response()->json($response, $code);
            }
        } catch (JWTException $e) {
            $message ='Authorization invalid';
            $response = ['code'=>$code,'message'=>$message];
            return response()->json($response, $code);
        }
        if (! $user) {
            $message ='user not found';
            $response = ['code'=>$code,'message'=>$message];
            return response()->json($response, $code);
        }
        return $next($request);
    }
}
