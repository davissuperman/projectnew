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
$param['code'] = '4271831580205313';
$param['ip'] = '115.29.185.117';
$param['language'] = 1;
$param['channel'] = 'X';
$result=$client->Get_CodeIsTrueByChannel($param);
echo ('<pre>');
var_dump ( $result );//获取服务器上数据类型
echo ('</pre>');