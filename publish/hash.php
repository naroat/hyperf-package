<?php

/*
 * id hash
 */
return [
    'default' => [
        'salt' => 'default-salt',                    //加密扰乱字符串
        'length' => 12,                              //加密后的长度
        'level' => 3                                 //加密后字符串等级:0,不限制;1,小写字母;2,小写字母+数字;3,大写字母+数字;
    ],
];