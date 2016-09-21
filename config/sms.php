<?php
/**
 * Created by yantao.
 * User: tao
 * Date: 2015/5/20
 * Time: 14:34
 */
return [
    'api'=>[
        'sms_login'=>[
            'id'      => 'SMS_5061003',
            'vars'    => ['code','product'],
            'desc'    => '登录确认验证码',
            'content' => '验证码${code}，您正在登录${product}，若非本人操作，请勿泄露。'
        ],
        'sms_check'=>[
            'id'      => 'SMS_5061002',
            'vars'    => ['code','product'],
            'desc'    => '身份验证验证码',
            'content' => '验证码${code}，您正在进行${product}身份验证，打死不要告诉别人哦！'
        ],
    ]
];
