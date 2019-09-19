<?php
/** 验证
 *
 *
 * @param validasi
 *
 * @return error
 */
function validation_message($validasi){

    $error = array();
    foreach ($validasi as $key => $value) {
        // $error[$key]=$value[0];
        $error[]=$value[0];
    }
    return $error;
}
/** 根据错误码获取错误信息
 *
 *
 * @param code
 *
 * @return error messaage
 */
function getError($code = '', $validator = null)
{
    $codeArr = config('error');
    if ($code !== '') {
        $code = [$code];
    }
    if (!is_null($validator)) {
        $code = validation_message($validator);
    }
    // dd($code);
    if (count($code) > 0) {
        if (array_key_exists($code[0], $codeArr)) {
            $response = [
                'code' => $code[0],
                'data' => $codeArr[$code[0]]
            ];
            return $response;
        }
    }
}
