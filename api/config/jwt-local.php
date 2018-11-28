<?php
return[
    'iss' => 'gaozhan.com', //jwt签发者
    'aud' => 'gaozhan.com', //jwt接收方
    'key' => 'HSKGJYTY5JHU6PO2', //加密key
    'exp' => 86400, //过期时间(秒)
    'nbf' => 0, //当前时间在nbf设定时间之前，该token无法使用
];


