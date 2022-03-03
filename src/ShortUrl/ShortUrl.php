<?php


namespace Taoran\HyperfPackage\ShortUrl;


class ShortUrl
{
    /**
     * 生成短url
     * @param $url
     * @return string
     */
    public function gen($url)
    {
        $url = crc32($url);
        $x = sprintf("%u", $url);
        $show = '';
        while ($x > 0) {
            $s = $x % 62;
            if ($s > 35) {
                $s = chr($s + 61);
            } elseif ($s > 9 && $s <= 35) {
                $s = chr($s + 55);
            }
            $show .= $s;
            $x = floor($x / 62);
        }
        return $show;
    }
}