<?php


//if(is_firstfuhao(">")){
//
//    echo "存在";
//}else{
//    echo "bu存在";
//};
//
//
//
//function is_firstfuhao($str){
//    $fuhaos= array("\"","“","'","<","《",);
//
//    return in_array($str,$fuhaos);
//
//}




//$temp_string="1234567890123456789!";
//$tmp_str_len=mb_strlen($temp_string);
//$texts = mb_substr($temp_string, $tmp_str_len-1, 1);
//
//echo $texts;


//$str = "香港加大内地？“水客”打击力度？您怎么看？";
//echo preg_replace($preg,$strPreg,$str);
//echo strtr($str,"？","_");
//echo str_replace("？","_",$str);


//echo str_replace_limit("？","$",$str,1);
//
//function str_replace_limit($search, $replace, $subject, $limit=-1) {
//// constructing mask(s)...
//    if (is_array($search)) {
//        foreach ($search as $k=>$v) {
//            $search[$k] = '`' . preg_quote($search[$k],'`') . '`';
//        }
//    }
//    else {
//        $search = '`' . preg_quote($search,'`') . '`';
//    }
//// replacement
//    return preg_replace($search, $replace, $subject, $limit);
//}
//
//$rg[1]=mb_ereg_replace("？","$",iconv('gb2312', 'utf-8',$str));




$str = "香港加大内?地“水客”打击力度！您怎么看?";

//$preg = "/(.*)(“)";//请补充
//$strPreg = "\\1<i>";//请补充
//echo preg_replace($preg,$strPreg,$str);

$needle= '?';
echo strripos($str, $needle,0);
echo str_replace($str,"$",strripos($str, $needle,0));
