<?php
return[
    'iss' => '', //jwt签发者
    'aud' => '', //jwt接收方
    'algs' => 'HS256', //加密方式(目前只支持HS256)
    'key' => '', //加密key
    'exp' => '', //过期时间(秒)
    'nbf' => '', //当前时间在nbf设定时间之前，该token无法使用
    
];


