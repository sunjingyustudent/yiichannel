<?php

/**
 *  检测数组变量是否存在，不存在则返回NULL
 * @DateTime 2016-11-01T10:18:07+0800
 * @param    [type]    string
 */
function is_array_set($array, $key, $default = null, $xss = true)
{
    if ($xss == true) {
        return isset($array[$key]) ? trim($array[$key]) : $default;
    }
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 *  检测数组变量是否存在，不存在则返回NULL
 * @DateTime 2016-11-01T10:18:07+0800
 * @param    [type]    string
 */
function is_array_set_int($array, $key, $default = null, $xss = true)
{
    if ($xss == true) {
        return isset($array[$key]) ? intval(trim($array[$key])) : $default;
    }
    return isset($array[$key]) ? intval($array[$key]) : $default;
}
/**
 * 生成随机字符串
 * @return   string
 */
function rand_str($length)
{
    $length = intval($length) < 8 ? 8 : $length;
    return substr(md5(uniqid(rand(), true)), 0, $length);
}

function object2array($object)
{
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
    } else {
        $array = $object;
    }
    return $array;
}

function ajaxDat($data = [], $msg = '', $code = 200)
{
    return [
        'code'  => $code,
        'msg'   => $msg,
        'data'  => $data
    ];
    exit;
}

function ajaxArrayIsNUllDat($page = 1)
{
    if ($page == 1) {
        return ajaxDat([], Yii::$app->params['api_msg_code']['3001'], 3001);
    }
    return ajaxDat([], Yii::$app->params['api_msg_code']['3999'], 3999);
}

function ajaxDatByCode($code = 300)
{
    return ajaxDat([], Yii::$app->params['api_msg_code'][$code], $code);
}
