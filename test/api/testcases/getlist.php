#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';

/**

title=测试API 获取测试用例列表;
cid=1
pid=1

使用正确产品id查询用例列表的状态码 >> 200
使用不存在的产品id查询用例列表的状态码 >> 200
使用正确产品id获取用例列表的数量 >> 50
使用不存在的产品id获取用例列表的数量 >> 0
使用正确产品id获取用例列表 >> 这个是测试用例51,这个是测试用例52

*/
global $token;
$header = array('Token' => $token->token);

$existProductCases = $rest->get('/products/2/testcases?page=1&limit=100&order=id_asc', $header);
$noProductCases    = $rest->get('/products/1000/testcases?page=1&limit=10&order=id_asc', $header);

r($existProductCases->status_code) && p() && e('200'); // 使用正确产品id查询用例列表的状态码
r($noProductCases->status_code)    && p() && e('200'); // 使用不存在的产品id查询用例列表的状态码

$existCases = $existProductCases->body->testcases;
$emptyCases = $noProductCases->body->testcases;

r(count($existCases)) && p() && e('50'); // 使用正确产品id获取用例列表的数量
r(count($emptyCases)) && p() && e('0');  // 使用不存在的产品id获取用例列表的数量

r($existCases) && p('title') && e('这个是测试用例51,这个是测试用例52,这个是测试用例53,这个是测试用例54,这个是测试用例55,这个是测试用例56,这个是测试用例57,这个是测试用例58,这个是测试用例59,这个是测试用例60,这个是测试用例61,这个是测试用例62,这个是测试用例63,这个是测试用例64,这个是测试用例65,这个是测试用例66,这个是测试用例67,这个是测试用例68,这个是测试用例69,这个是测试用例70,这个是测试用例71,这个是测试用例72,这个是测试用例73,这个是测试用例74,这个是测试用例75,这个是测试用例76,这个是测试用例77,这个是测试用例78,这个是测试用例79,这个是测试用例80,这个是测试用例81,这个是测试用例82,这个是测试用例83,这个是测试用例84,这个是测试用例85,这个是测试用例86,这个是测试用例87,这个是测试用例88,这个是测试用例89,这个是测试用例90,这个是测试用例91,这个是测试用例92,这个是测试用例93,这个是测试用例94,这个是测试用例95,这个是测试用例96,这个是测试用例97,这个是测试用例98,这个是测试用例99,这个是测试用例100'); // 使用正确产品id获取用例列表
