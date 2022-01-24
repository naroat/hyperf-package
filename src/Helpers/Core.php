<?php

declare(strict_types=1);

namespace Taoran\HyperfPackage\Helpers;


if (! function_exists('encrypt_password')) {
    function encrypt_password($password, $password_salt): string
    {
        return md5(md5($password) . md5($password_salt));
    }
}

/*
 * POST json请求
 */
function post_json($method, $url, $post, $headers = ['Accept' => 'application/json'])
{
    $client = new \GuzzleHttp\Client();
    $response = $client->request($method, $url, ['json' => $post, 'headers' => $headers]);
    $response->getStatusCode(); // 200
    return $response->getBody()->getContents();
}


/*
 * POST json请求
 */
function post_api($method, $url, $post)
{
    $client = new \GuzzleHttp\Client();
    $response = $client->request($method, $url, ['form_params' => $post]);
    $response->getStatusCode(); // 200
    return $response->getBody()->getContents();
}

if (! function_exists('toArray')) {
    /**
     * toArray
     * 对象转数组.
     * @param $object
     * @return bool
     */
    function toArray($object)
    {
        if (! is_object($object)) {
            return $object;
        }
        return json_decode(json_encode($object), true);
    }
}

if (! function_exists('toObject')) {
    /**
     * 数组 转 对象
     *
     * @param array $arr 数组
     * @return object
     */
    function toObject($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || gettype($v) == 'object') {
                $arr[$k] = (object) array_to_object($v);
            }
        }

        return (object) $arr;
    }
}

if (! function_exists('getWeekIntervalByYearMonth')) {
    /**
     * @param string $year_month
     * @param string $format
     * @return array
     */
    function getWeekIntervalByYearMonth($year_month = '', $format = 'Y-m-d')
    {
        $year_month = $year_month != '' ? $year_month : date('Y-m', time());
        //php获取当前月份的所有天数
        $total_day = date('d', strtotime("{$year_month} + 1 month -1 day"));
        $date = [
            'start' => $year_month . '-' . '01',
            'end' => $year_month . '-' . $total_day,
        ];

        //创建一个空数组
        $weekInterval = [];
        $i = 1;
        $j = 1;
        while (true) {
            //计算第一天是周几
            $day_number = date('N', strtotime($year_month . '-' . $i));

            //每周第一天
            $week_start_day = $i - ($day_number - 1) < 1 ? 1 : $i - ($day_number - 1);

            //如果每周的第一天等于总天数，最后一天则等于总天数
            if ($week_start_day >= $total_day) {
                $week_start_day = $total_day;
            }

            //每周最后一天最大是总天数
            $week_end_day = $i + (7 - $day_number) >= $total_day ? $total_day : $i + (7 - $day_number);

            //本周起始日期
            $start_date = date($format, strtotime($year_month . '-' . $week_start_day));

            //本周结束日期
            $end_date = date($format, strtotime($year_month . '-' . $week_end_day));

            //当周开始的时间 与结束时间
            $weekInterval[] = [
                'start_date' => $start_date, //本周起始日期
                'end_date' => $end_date, //本周结束日期
                'start' => strtotime($start_date), //本周起始时间戳
                'end' => strtotime($end_date) + 86399, //本周结束时间戳
                'week_th' => $j++, //本周结束时间戳
            ];

            $i = $i + 7;
            if ($week_end_day == $total_day) { //如果本周的最后一天等于总天数跳出循环
                break;
            }
        }

        return $weekInterval;
    }
}

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式).
 * @param string $day1
 * @param string $day2
 * @return number
 */
function diffBetweenTwoDays($day1, $day2)
{
    $second1 = strtotime($day1);
    $second2 = strtotime($day2);

    if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
    }
    return ($second1 - $second2) / 86400;
}

if (! function_exists('getthemonth')) {
    /**
     * //获取具体日期的函数.
     * @param $date
     * @return array
     */
    function getthemonth($date)
    {
        $firstday = date('Y-m-01', strtotime($date));
        $lastday = date('Y-m-d', strtotime("{$firstday} +1 month -1day"));
        return [$firstday, $lastday];
    }
}

if (! function_exists('getDateByInterval')) {
    /**
     * 查询指定时间范围内的所有日期，月份，季度，年份.
     *
     * @param $startDate   指定开始时间，Y-m-d格式
     * @param $endDate     指定结束时间，Y-m-d格式
     * @param $type        类型，day 天，month 月份，quarter 季度，year 年份
     * @return array
     */
    function getDateByInterval($startDate, $endDate, $type)
    {
        if (date('Y-m-d', strtotime($startDate)) != $startDate || date('Y-m-d', strtotime($endDate)) != $endDate) {
            return '';
        }

        $tempDate = $startDate;
        $returnData = [];
        $i = 0;
        if ($type == 'day') {    // 查询所有日期
            while (strtotime($tempDate) < strtotime($endDate)) {
                $tempDate = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($startDate)));
                $returnData[] = $tempDate;
                ++$i;
            }
        } elseif ($type == 'week') { //查询所有周
            while (strtotime($tempDate) < strtotime($endDate)) {
                $temp = [];
                $idx = strftime('%u', strtotime($startDate . '+' . $i . 'day'));
                $mon_idx = $idx - 1;
                $sun_idx = $idx - 7;
                $startDate_idx = strtotime($startDate . '+' . $i . 'day') - $mon_idx * 86400 > strtotime($startDate) ? strtotime($startDate . '+' . $i . 'day') - $mon_idx * 86400 : strtotime($startDate);
                $endDate_idx = strtotime($startDate . '+' . $i . 'day') - $sun_idx * 86400 < strtotime($endDate) ? strtotime($startDate . '+' . $i . 'day') - $sun_idx * 86400 : strtotime($endDate);
                $temp['startDate'] = strftime('%Y-%m-%d', $startDate_idx);
                $temp['endDate'] = strftime('%Y-%m-%d', $endDate_idx);
                $tempDate = $temp['endDate'];
                $returnData[] = $temp;
                $i = $i + 7;
            }
        } elseif ($type == 'month') {    // 查询所有月份以及开始结束时间
            while (strtotime($tempDate) < strtotime($endDate)) {
                $temp = [];
                $month = strtotime('+' . $i . ' month', strtotime($startDate));
                $temp['name'] = date('Y-m', $month);
                $startDate_month = strtotime(date('Y-m-01', $month)) > strtotime($startDate) ? date('Y-m-01', $month) : $startDate;
                $endDate_month = strtotime(date('Y-m-t', $month)) < strtotime($endDate) ? date('Y-m-t', $month) : $endDate;
                $temp['startDate'] = $startDate_month;
                $temp['endDate'] = $endDate_month;
                $tempDate = $temp['endDate'];
                $returnData[] = $temp;
                ++$i;
            }
        } elseif ($type == 'quarter') {    // 查询所有季度以及开始结束时间
            while (strtotime($tempDate) < strtotime($endDate)) {
                $temp = [];
                $quarter = strtotime('+' . $i . ' month', strtotime($startDate));
                $q = ceil(date('n', $quarter) / 3);
                $temp['name'] = date('Y', $quarter) . '第' . $q . '季度';
                $temp['startDate'] = date('Y-m-01', mktime((int) 0, (int) 0, (int) 0, (int) ($q * 3 - 3 + 1), (int) 1, (int) (date('Y', $quarter))));
                $temp['endDate'] = date('Y-m-t', mktime((int) 23, (int) 59, (int) 59, (int) ($q * 3), (int) 1, (int) (date('Y', $quarter))));
                $tempDate = $temp['endDate'];
                $returnData[] = $temp;
                $i = $i + 3;
            }
        } elseif ($type == 'year') {    // 查询所有年份以及开始结束时间
            while (strtotime($tempDate) < strtotime($endDate)) {
                $temp = [];
                $year = strtotime('+' . $i . ' year', strtotime($startDate));
                $temp['name'] = date('Y', $year) . '年';
                $startDate_year = strtotime(date('Y-01-01', $year)) > strtotime($startDate) ? date('Y-01-01', $year) : $startDate;
                $endDate_year = strtotime(date('Y-12-31', $year)) < strtotime($endDate) ? date('Y-12-31', $year) : $endDate;
                $temp['startDate'] = $startDate_year;
                $temp['endDate'] = $endDate_year;
                $tempDate = $temp['endDate'];
                $returnData[] = $temp;
                ++$i;
            }
        }
        return $returnData;
    }
}

/**
 * +----------------------------------------------------------
 * 将一个字符串部分字符用*替代隐藏
 * +----------------------------------------------------------.
 * @param string $string 待转换的字符串
 * @param int $bengin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int $len 需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int $type 转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string $glue 分割符
 *                     +----------------------------------------------------------
 * @return string 处理后的字符串
 *                +----------------------------------------------------------
 */
function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = '@')
{
    if (empty($string)) {
        return false;
    }
    $array = [];
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, 'utf8');
            $string = mb_substr($string, 1, $strlen, 'utf8');
            $strlen = mb_strlen($string);
        }
    }
    if ($type == 0) {
        for ($i = $bengin; $i < ($bengin + $len); ++$i) {
            if (isset($array[$i])) {
                $array[$i] = '*';
            }
        }
        $string = implode('', $array);
    } elseif ($type == 1) {
        $array = array_reverse($array);
        for ($i = $bengin; $i < ($bengin + $len); ++$i) {
            if (isset($array[$i])) {
                $array[$i] = '*';
            }
        }
        $string = implode('', array_reverse($array));
    } elseif ($type == 2) {
        $array = explode($glue, $string);
        $array[0] = hideStr($array[0], $bengin, $len, 1);
        $string = implode($glue, $array);
    } elseif ($type == 3) {
        $array = explode($glue, $string);
        $array[1] = hideStr($array[1], $bengin, $len, 0);
        $string = implode($glue, $array);
    } elseif ($type == 4) {
        $left = $bengin;
        $right = $len;
        $tem = [];
        for ($i = 0; $i < ($length - $right); ++$i) {
            if (isset($array[$i])) {
                $tem[] = $i >= $left ? '*' : $array[$i];
            }
        }
        $array = array_chunk(array_reverse($array), $right);
        $array = array_reverse($array[0]);
        for ($i = 0; $i < $right; ++$i) {
            $tem[] = $array[$i];
        }
        $string = implode('', $tem);
    }
    return $string;
}

if (! function_exists('cut_str')) {
    /**
     * 按符号截取字符串的指定部分.
     * @param string $str 需要截取的字符串
     * @param string $sign 需要截取的符号
     * @param int $number 如是正数以0为起点从左向右截 负数则从右向左截
     * @return string 返回截取的内容
     */
    function cut_str($str, $sign, $number)
    {
        $array = explode($sign, $str);
        $length = count($array);
        if ($number < 0) {
            $new_array = array_reverse($array);
            $abs_number = abs($number);
            if ($abs_number > $length) {
                return 'error';
            }
            return $new_array[$abs_number - 1];
        }
        if ($number >= $length) {
            return 'error';
        }
        return $array[$number];
    }
}


if (! function_exists('deep_external')) {
    /**
     * //深度转化目标.
     * @param mixed $deep_external
     * @return string
     */
    function deep_external($deep_external)
    {
        switch ($deep_external) {
            case 'AD_CONVERT_TYPE_ACTIVE_REGISTER':
                return '注册';
            case 'AD_CONVERT_TYPE_NEXT_DAY_OPEN':
                return '次留';
            case 'AD_CONVERT_TYPE_LT_ROI':
                return '广告变现ROI';
            case 'AD_CONVERT_TYPE_PURCHASE_ROI':
                return '付费ROI';
            case 'AD_CONVERT_TYPE_GAME_ADDICTION':
                return '关键行为';
            default:
                return '无';
        }
    }
}

if (! function_exists('convert_type')) {
    /**
     * //转化类型.
     * @param mixed $convert_type
     * @return string
     */
    function convert_type($convert_type)
    {
        switch ($convert_type) {
            case 'AD_CONVERT_TYPE_DOWNLOAD_FINISH':
                return '下载完成';
            case 'AD_CONVERT_TYPE_ACTIVE':
                return '激活';
            case 'AD_CONVERT_TYPE_ACTIVE_REGISTER':
                return '激活且注册';
            case 'AD_CONVERT_TYPE_PAY':
                return '激活且付费';
            case 'AD_CONVERT_TYPE_INSTALL_FINISH':
                return '安装完成';
            case 'AD_CONVERT_TYPE_NEXT_DAY_OPEN':
                return '激活且次留';
            default:
                return '无';
        }
    }
}

if (! function_exists('convert_source_type')) {
    /**
     * //转化类型.
     * @param mixed $convert_source_type
     * @return string
     */
    function convert_source_type($convert_source_type)
    {
        switch ($convert_source_type) {
            case 'AD_CONVERT_SOURCE_TYPE_XPATH':
                return '路径转化';
            case 'AD_CONVERT_SOURCE_TYPE_APP_DOWNLOAD':
                return '应用下载API';
            case 'AD_CONVERT_SOURCE_TYPE_H5_API':
                return '落地页API（H5）';
            case 'AD_CONVERT_SOURCE_TYPE_SDK':
                return '应用下载SDK';
            case 'AD_CONVERT_SOURCE_TYPE_OPEN_URL':
                return '应用直达API（应用直达链接）';
            case 'AD_CONVERT_SOURCE_TYPE_NORMAL_APP_DOWNLOAD':
                return '普通应用下载';
            default:
                return '无';
        }
    }
}

if (! function_exists('ad_status')) {
    /**
     * //广告计划投放状态.
     * @param mixed $ad_status
     * @return string
     */
    function ad_status($ad_status)
    {
        switch ($ad_status) {
            case 'AD_STATUS_DELIVERY_OK':
                return '投放中';
            case 'AD_STATUS_DISABLE':
                return '计划暂停';
            case 'AD_STATUS_AUDIT':
                return '新建审核中';
            case 'AD_STATUS_REAUDIT':
                return '修改审核中';
            case 'AD_STATUS_DONE':
                return '已完成（投放达到结束时间）';
            case 'AD_STATUS_CREATE':
                return '计划新建';
            case 'AD_STATUS_AUDIT_DENY':
                return '审核不通过';
            case 'AD_STATUS_BALANCE_EXCEED':
                return '账户余额不足';
            case 'AD_STATUS_BUDGET_EXCEED':
                return '超出预算';
            case 'AD_STATUS_NOT_START':
                return '未到达投放时间';
            case 'AD_STATUS_NO_SCHEDULE':
                return '不在投放时段';
            case 'AD_STATUS_CAMPAIGN_DISABLE':
                return '已被广告组暂停';
            case 'AD_STATUS_CAMPAIGN_EXCEED':
                return '广告组超出预算';
            case 'AD_STATUS_DELETE':
                return '已删除';
            case 'AD_STATUS_ALL':
                return '所有包含已删除';
            case 'AD_STATUS_NOT_DELETE':
                return '所有不包含已删除（状态过滤默认值）';
            case 'AD_STATUS_ADVERTISER_BUDGET_EXCEED':
                return '超出广告主日预算';
            default:
                return '未知';
        }
    }
}

if (! function_exists('delivery_range')) {
    /**
     * //广告投放范围.
     * @param mixed $delivery_range
     * @return string
     */
    function delivery_range($delivery_range)
    {
        switch ($delivery_range) {
            case 'DEFAULT':
                return '默认';
            case 'UNION':
                return '只投放到资讯联盟（穿山甲）';
            case 'UNIVERSAL':
                return '通投智选';
            default:
                return '未知';
        }
    }
}

if (! function_exists('download_type')) {
    /**
     * //应用下载方式.
     * @param mixed $download_type
     * @return string
     */
    function download_type($download_type): string
    {
        switch ($download_type) {
            case 'DOWNLOAD_URL':
                return '下载链接';
            case 'QUICK_APP_URL':
                return '快应用+下载链接';
            case 'EXTERNAL_URL':
                return '落地页链接';
            default:
                return '未知';
        }
    }
}

if (! function_exists('pricing_type')) {
    /**
     * //计划出价类型.
     * @param mixed $pricing_type
     * @return string
     */
    function pricing_type($pricing_type)
    {
        switch ($pricing_type) {
            case 'PRICING_CPC':
                return 'CPC（点击付费）';
            case 'PRICING_CPM':
                return '快应用+CPM（展示付费）';
            case 'PRICING_OCPC':
                return 'OCPC';
            case 'PRICING_OCPM':
                return 'OCPM（转化量付费）';
            case 'PRICING_CPV':
                return 'CPV';
            default:
                return '未知';
        }
    }
}

if (! function_exists('flow_control_mode')) {
    /**
     * //竞价策略(投放方式), .
     * @param mixed $flow_control_mode
     * @return string
     */
    function flow_control_mode($flow_control_mode): string
    {
        switch ($flow_control_mode) {
            case 'FLOW_CONTROL_MODE_FAST':
                return '优先跑量';
            case 'FLOW_CONTROL_MODE_SMOOTH':
                return '优先低成本';
            case 'FLOW_CONTROL_MODE_BALANCE':
                return '均衡投放';
            default:
                return '未知';
        }
    }
}

if (! function_exists('inventory_type')) {
    /**
     * //竞价策略(投放方式), .
     * @param mixed $inventory_type
     * @return string
     */
    function inventory_type($inventory_type)
    {
        switch ($inventory_type) {
            case 'INVENTORY_FEED':
                return '头条';
            case 'INVENTORY_VIDEO_FEED':
                return '西瓜';
            case 'INVENTORY_HOTSOON_FEED':
                return '火山';
            case 'INVENTORY_AWEME_FEED':
                return '抖音';
            case 'INVENTORY_UNION_SLOT':
                return '穿山甲';
            default:
                return '未知';
        }
    }
}

if (! function_exists('ban_type')) {
    /**
     * //封号类型.
     * @param mixed $ban_type
     * @return string
     */
    function ban_type($ban_type): string
    {
        switch ($ban_type) {
            case '1':
                return '封禁设备';
            case '2':
                return '封禁IP';
            case '3':
                return '封禁账号';
            case '4':
                return '封禁全部';
            default:
                return '未知';
        }
    }
}


if(! function_exists('randString')){
    /**
     * 产生随机字串，可用来自动生成密码
     * 默认长度6位 字母和数字混合 支持中文
     * @param string $len 长度
     * @param string $type 字串类型
     * 0 字母 1 数字 其它 混合
     * @param string $addChars 额外字符
     * @return string
     */
    function randString($len = 6, $type = '', $addChars = '')
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
                break;
            case 5:
                $chars='ABCDEFGHJKLMNPQRSTUVWXY'.$addChars;
                break;
            default:
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($len > 10) {
//位数过长重复字符串一定次数
            $chars = 1 == $type ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        if (4 != $type) {
            $chars = str_shuffle($chars);
            $str   = substr($chars, 0, $len);
        } else {
            // 中文随机字
            for ($i = 0; $i < $len; $i++) {
                $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1, 'utf-8', false);
            }
        }
        return $str;
    }
}


if (! function_exists('mFristAndLast')) {
    /**
     * 获取指定月份的第一天开始和最后一天结束的时间戳
     *
     * @param int $y 年份 $m 月份
     * @return array(本月开始时间，本月结束时间)
     */
    function mFristAndLast($y = "2021", $m = ""){
        if ($y == "") $y = date("Y");
        if ($m == "") $m = date("m");
        $m = sprintf("%02d", intval($m));
        $y = str_pad((string)($y), 4, "0", STR_PAD_RIGHT);

        $m>12 || $m<1 ? $m=1 : $m=$m;
        $firstday = strtotime($y . $m . "01000000");
        $firstdaystr = date("Y-m-01", $firstday);
        $lastday = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdaystr +1 month -1 day")));
        return array(
            "firstday" => $firstday,
            "lastday" => $lastday
        );
    }
}

if (! function_exists('age_type')) {
    /**
     * //年龄说明
     * @param mixed $age_type
     * @return string
     */
    function age_type($age_type): string
    {
        switch ($age_type) {
            case '31-40岁':
                return '31-40岁';
            case '24-30岁':
                return '24-30岁';
            case '41-50岁':
                return '41-49岁';
            case '41-49岁':
                return '41-49岁';
            case '18-23岁':
                return '18-23岁';
            case '50岁以上':
                return '50岁以上';
            case '其他':
                return '其他';
            case '50+岁':
                return '50岁以上';
            default:
                return '未知';
        }
    }
}

/**
 * 设置保存数据（主要过滤实体，防止xss）
 * @param Object $model
 * @param array $data
 * @return object
 */
if (!function_exists('set_save_data')) {
    function set_save_data(\Hyperf\Database\Model\Model $model, array $data)
    {
        foreach ($data as $key => $v) {
            if (is_string($v)) {
                //转换html内容
                $model->$key = htmlspecialchars($v, ENT_QUOTES);
            }
        }
        return $model;
    }
}

/**
 * orm打印带参数的sql
 * @param $model
 * @return string
 */
if (!function_exists('orm_sql')) {
    function orm_sql($model) {
        $bindings = $model->getBindings();
        $sql = str_replace('?', '%s', $model->toSql());
        $tosql = sprintf($sql, ...$bindings);
        return $tosql;
    }
}

/**
 * 判断数据库是否存在
 * return true:存在
 */
if (!function_exists('db_exists')) {
    function db_exists($dbname)
    {
        $flag = true;
        $host = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $dbh = mysqli_connect($host, $username, $password);
        $select_db = mysqli_query($dbh, 'use ' . $dbname);
        if (!$select_db) {
            $flag = false;
        }
        return $flag;
    }
}