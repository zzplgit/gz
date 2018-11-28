<?php
$sms = require(__DIR__ . '/sms.php');

$sms = array_merge(
    require(__DIR__ . '/sms.php'),
    require(__DIR__ . '/sms-local.php')
);

$jwt = array_merge(
    require(__DIR__ . '/jwt.php'),
    require(__DIR__ . '/jwt-local.php')
);

return [
    'jwt' => $jwt,
    'sms' => $sms,
    'user_role' => [
        0 => 'admin',
        100 => '校长',
        101 => '校长-未设置密码',
        200 => '教师',
        300 => '学生'
    ],
    'sex' => [
        'MALE' => '男',
        'FEMALE' => '女'
    ],
    'f_structure' => [
        'NORMAL' => '双亲',
        'SINGLE' => '单亲',
        'SECR' => '不知道'
    ]
];
