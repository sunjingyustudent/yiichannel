<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/9/12
 * Time: 下午8:06
 */
namespace app\common\widgets;

class Xml {

    public static function toXml($array, $useCdata = true, $xml = "<xml>", $xmlKey = "</xml>") 
    {
         
        foreach ($array as $key=>$val)
        {
            if(is_array($val)) {
                $xml.="<".$key.">";
                $xmlKey ="</".$key.">".$xmlKey;
                return self::toXml($val, $useCdata, $xml, $xmlKey);
            }
            if (is_numeric($val) || !$useCdata){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.= $xmlKey == "</xml>" ?  $xmlKey : $xmlKey;
        return $xml;
    }
    
    public static function xml($data) 
    {
        $xmlstart = "";
        $xmlend = "";
        if (is_array($data)) {
            foreach ($data as $item => $val) {
                if (is_array($val)) {
                    if (count($val) == 1) {
                        $xmlstart .= "<" . $item . ">";
                        $xmlend .= "</" . $item . ">" . $xmlend;
                        $xmlstart .= self::xml($val);
                    } else {
                        $numreg = "/\d/";
                        if (preg_match_all($numreg, $item)) {
                            $item = preg_replace($numreg, '', $item);
                        }
                        $xmlstart .= "<" . $item . ">";
                        $xmlstart .= self::xml($val);
                        $xmlstart .= "</" . $item . ">" . $xmlend;
                    }
                } else if (is_numeric($val)) {
                    $xmlstart .= "<" . $item . ">" . $val . "</" . $item . ">";
                } else {
                    $xmlstart .= "<" . $item . "><![CDATA[" . $val . "]]></" . $item . ">";
                }
            }
        }

        return $xmlstart . $xmlend;
    }
  
  
    
}