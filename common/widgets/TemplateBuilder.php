<?php
/**
 * Created by PhpStorm.
 * User: Jhu
 * Date: 17/2/26
 * Time: 下午8:36
 */

namespace app\common\widgets;

class TemplateBuilder {

    public static function build($param,$openid)
    {
    
       
        if (!empty($param))
        {
            switch ($param['keyword_num'])
            {
                case 1 :
                    $data = array(
                        'first' => array('value' => $param['firstValue']),
                        'keyword1' => array('value' => $param['key1word']),
                        'remark' => array('value' => $param['remark'])
                    );

                    break;

                case 2 :
                    $data = array(
                        'first' => array('value' => $param['firstValue']),
                        'keyword1' => array('value' => $param['key1word']),
                        'keyword2' => array('value' => $param['key2word']),
                        'remark' => array('value' => $param['remark'])
                    );

                    break;

                case 3 :
                    if (is_array($param['key2word'])|| is_array($param['key1word'])||is_array($param['key3word'])) 
                    {
                        if(is_array($param['key1word'])){
                            $keyword1 = $param['key1word'];
                        }else{
                            $keyword1 = array('value' => $param['key1word']);
                        }
                         if(is_array($param['key2word'])){
                            $keyword2 = $param['key2word'];
                        }else{
                            $keyword2 = array('value' => $param['key2word']);
                        }
                         if(is_array($param['key3word'])){
                            $keyword3 = $param['key3word'];
                        }else{
                            $keyword3 = array('value' => $param['key3word']);
                        }
                        $data = array(
                            'first' => array('value' => $param['firstValue']),
                            'keyword1' => $keyword1,
                            'keyword2' => $keyword2,
                            'keyword3' => $keyword3,
                            'remark' => array('value' => $param['remark'])
                        );
                    } else {
                        $data = array(
                            'first' => array('value' => $param['firstValue']),
                            'keyword1' => array('value' => $param['key1word']),
                            'keyword2' => array('value' => $param['key2word']),
                            'keyword3' => array('value' => $param['key3word']),
                            'remark' => array('value' => $param['remark'])
                        );
                    }


                    break;

                case 4 :
                    $data = array(
                        'first' => array('value' => $param['firstValue']),
                        'keyword1' => array('value' => $param['key1word']),
                        'keyword2' => array('value' => $param['key2word']),
                        'keyword3' => array('value' => $param['key3word']),
                        'keyword4' => array('value' => $param['key4word']),
                        'remark' => array('value' => $param['remark'])
                    );

                    break;
            }

            $message = array (
                'touser' => $openid,
                'template_id' => $param['template_id'],
                'url' => $param['url'],
                'data' => $data
            );

            return $message;

        } else {
            //错误处理
            return false;
        }
    }
}