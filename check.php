<?php

header ( "Content-Type: text/html; charset=utf-8" );
/*
* 指定WebService路径并初始化一个WebService客户端
*/
$ws = "http://digitcode.yesno.com.cn/CCNOutService/OutDigitCodeService.asmx?wsdl";//webservice服务的地址
$client = new SoapClient ($ws);
/*
* 获取SoapClient对象引用的服务所提供的所有方法
*/
//echo ("SOAP服务器提供的开放函数:");
//echo ('<pre>');
//var_dump ( $client->__getFunctions () );//获取服务器上提供的方法
//echo ('</pre>');
//echo ("SOAP服务器提供的Type:");

$param['directoryName'] = '9457';
$param['mima'] = 'ST@47A4SKE';
$param['code'] = '1234567812345678';
$param['ip'] = '127.0.0.1';
$param['language'] = 1;
$param['channel'] = 'X';
$result=$client->Get_CodeIsTrueByChannel($param);//查询中国郑州的天气，返回的是一个结构体
echo ('<pre>');
var_dump ( $result );//获取服务器上数据类型
echo ('</pre>');