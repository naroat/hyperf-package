<?php

namespace Taoran\HyperfPackage\Helpers;

/**
 * 获取毫秒级别的时间戳
 */
if (!function_exists('get_msectime')) {
    function get_msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return (int)$msectime;
    }
}

/**
 * 毫秒转日期
 * @params $msectime int 毫秒时间戳
 */
if (!function_exists('get_msec_to_mescdate')) {
    function get_msec_to_mescdate($msectime, $format = "Y-m-d H:i:s.x")
    {
        $msectime = $msectime * 0.001;
        if (strstr($msectime, '.')) {
            sprintf("%01.3f", $msectime);
            list($usec, $sec) = explode(".", $msectime);
            $sec = str_pad($sec, 3, "0", STR_PAD_RIGHT);
        } else {
            $usec = $msectime;
            $sec = "000";
        }
        $date = date($format, $usec);
        return $mescdate = str_replace('x', $sec, $date);
    }
}

/**
 * 日期转毫秒
 * @params string 日期字符串 eg:2018-09-21 15:30:29.304
 */
if (!function_exists('get_date_to_mesc')) {
    function get_date_to_mesc($mescdate)
    {
        $mescdate_arr = explode(".", $mescdate);
        $usec = $mescdate_arr[0];
        $sec = isset($mescdate_arr[1]) ? $mescdate_arr[1] : 0;
        $date = strtotime($usec);
        $return_data = str_pad($date . $sec, 13, "0", STR_PAD_RIGHT);
        return (int)$return_data;
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
