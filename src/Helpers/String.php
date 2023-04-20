<?php
declare(strict_types=1);

namespace Taoran\HyperfPackage\Helpers;

/**
 * 生成随机数.
 * @param number $length
 * @return number
 */
if (! function_exists('generate_number')) {
    function generate_number($length = 6)
    {
        return rand(pow(10, ($length - 1)), pow(10, $length) - 1);
    }
}

/**
 * 生成随机字符串.
 * @param number $length
 * @param string $chars
 * @return string
 */
if (! function_exists('generate_string')) {
    function generateString($length = 6, $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz')
    {
        $chars = str_split($chars);

        $chars = array_map(function ($i) use ($chars) {
            return $chars[$i];
        }, array_rand($chars, $length));

        return implode($chars);
    }
}


/**
 * 人民币转换大写
 */
if (!function_exists('rmb_upper')) {
    function rmb_upper($num)
    {
        $num = round($num,2);  //取两位小数
        $num = ''.$num;  //转换成数字
        $arr = explode('.',$num);

        $str_left = $arr[0];
        $str_right = $arr[1] ?? 0;

        $len_left = strlen($str_left); //小数点左边的长度
        $len_right = strlen($str_right); //小数点右边的长度

        //循环将字符串转换成数组，
        for($i=0;$i<$len_left;$i++)
        {
            $arr_left[] = substr($str_left,$i,1);
        }

        for($i=0;$i<$len_right;$i++)
        {
            $arr_right[] = substr($str_right,$i,1);
        }

        //构造数组$daxie
        $daxie = array(
            '0'=>'零',
            '1'=>'壹',
            '2'=>'贰',
            '3'=>'叁',
            '4'=>'肆',
            '5'=>'伍',
            '6'=>'陆',
            '7'=>'柒',
            '8'=>'捌',
            '9'=>'玖',
        );

        //循环将数组$arr_left中的值替换成大写
        foreach($arr_left as $k => $v)
        {
            $arr_left[$k] = $daxie[$v];
            switch($len_left--)
            {
                //数值后面追加金额单位
                case 5:
                    $arr_left[$k] .= '万';break;
                case 4:
                    $arr_left[$k] .= '千';break;
                case 3:
                    $arr_left[$k] .= '百';break;
                case 2:
                    $arr_left[$k] .= '十';break;
                default:
                    $arr_left[$k] .= '元';break;
            }
        }

        foreach($arr_right as $k =>$v)
        {
            $arr_right[$k] = $daxie[$v];
            switch($len_right--)
            {
                case 2:
                    $arr_right[$k] .= '角';break;
                default:
                    $arr_right[$k] .= '分';break;
            }
        }

        //将数组转换成字符串，并拼接在一起
        $new_left_str = implode('',$arr_left);
        $new_right_str = implode('',$arr_right);

        $new_str = $new_left_str.$new_right_str;

        //如果金额中带有0，大写的字符串中将会带有'零千零百零十',这样的字符串，需要替换掉
        $new_str = str_replace('零万','零',$new_str);
        $new_str = str_replace('零千','零',$new_str);
        $new_str = str_replace('零百','零',$new_str);
        $new_str = str_replace('零十','零',$new_str);
        $new_str = str_replace('零零零','零',$new_str);
        $new_str = str_replace('零零','零',$new_str);
        $new_str = str_replace('零元','元',$new_str);
        if ($new_str == "元零分") {
            $new_str = '零元零分';
        }
        return $new_str;
    }
}


if (!function_exists('encode_hashids')) {
    /**
     * 加密数字id到hashid
     * @param $name
     * @param $id
     * @return bool|string
     */
    function encode_hashids($name, $id)
    {
        $config = config('hash.' . $name);

        if (empty($config)) {
            return false;
        }

        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        //实例化Hashids
        switch ($config['level']) {
            case 1:
                $alphabet = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case 2:
                $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';
                break;
            case 3:
                $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                break;
        }

        $hashids = new \Hashids\Hashids($config['salt'], $config['length'], $alphabet);
        $str = $hashids->encode($id);
        unset($hashids);
        return $str;
    }
}

if (!function_exists('decode_hashids')) {
    /**
     * 解密数字id到hashid
     * @param $name
     * @param $hashid
     * @return bool
     */
    function decode_hashids($name, $hashid)
    {
        $config = config('hash.' . $name);

        if (empty($config)) {
            return false;
        }

        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        //实例化Hashids
        switch ($config['level']) {
            case 1:
                $alphabet = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case 2:
                $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';
                break;
            case 3:
                $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                break;
        }

        $hashids = new \Hashids\Hashids($config['salt'], $config['length'], $alphabet);
        $ids = $hashids->decode($hashid);
        unset($hashids);
        if (!isset($ids[0])) {
            return false;
        }
        return $ids[0];
    }
}

if (!function_exists('hide_email')) {
    /**
     * 私隐化邮箱
     * @param $email
     * @return string
     */
    function hide_email($email)
    {
        if (empty($email)) {
            return '';
        }

        $email_array = explode("@", $email);
        $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($email, 0, 3); //邮箱前缀
        $count = 0;
        $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $email, -1, $count);
        $rs = $prevfix . $str;
        return $rs;
    }
}

if (!function_exists('hide_phone')) {
    /**
     * 私隐化手机号码
     * @param $phone
     * @return string
     */
    function hide_phone($phone)
    {
        if (empty($phone)) {
            return '';
        }

        $str = substr_replace($phone, '****', 3, 4);

        return $str;
    }
}

if (!function_exists('cut_html')) {
    /**
     * 去掉富文本标签
     * @param $content
     * @return string
     */
    function cut_html($content, $length = 100)
    {
        $content_01 = $content;//从数据库获取富文本content
        $content_02 = htmlspecialchars_decode($content_01);//把一些预定义的 HTML 实体转换为字符
        $content_03 = str_replace("&nbsp;", "", $content_02);//将空格替换成空
        $contents = strip_tags($content_03);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
        $con = mb_substr($contents, 0, $length, "utf-8");//返回字符串中的前100字符串长度的字符
        return $con;
    }
}