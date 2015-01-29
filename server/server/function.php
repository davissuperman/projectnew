<?php

//回调函数们
//有新用户时
function user_add_callback($ws, $index, $role) {
    
}

//关闭时
function close_callback($ws, $index) {
    // if ($ws->serviceLise[$index]) {//如果是客服的就吧客服在数组中删除
    // file_put_contents('d:/close_callback.txt', '333');
    //   unset($ws->serviceLise[$index]);
    //  }
    //客服退出了告诉其他客服
}

//发送消息时
function send_callback($data, $index, $ws) {
//判断是客服 还是客户   
    $info = json_decode($data, TRUE);
    if ($info['role'] && $info['role'] == 's') {//是客服发的信息
        if ($info['name']) {//如果是客服刚登录进来 把客户信息存到客服队列---name用来区分是不是刚登录
            $ws->serviceLise[$index] = $info;
           // file_put_contents("d:/d.txt", var_export($data, TRUE));
            //诉告诉所有客服有新客服登录
            send_to_all_service($data, 's', $ws);
            //吧已经在线的客服信息返回到自己的登录界面
        }
    } else {//是客户发的信息
        // file_put_contents("d:/11.txt", $data);
        // send_to_all_service($data, 'service', $ws);
        //客户把信息发给指定客服
        send_to_all_service($data, 'f', $ws);
        //  send_to_service_id($data, $ws);
    }
}

/**
 * 给指定客服发信息
 */
function send_to_service_id($data, $ws) {
    $info = json_decode($data, TRUE);
    // file_put_contents("d:/uid.txt", $info);
    $res = array(
        'msg' => $data,
        'type' => 'text',
    );
    // $res = $ws->frame($res);
   // file_put_contents("d:/info.txt", $res);
    foreach ($ws->serviceLise as $key => $v) {
        if ($v['uid'] == $info['uid']) {
            ///   file_put_contents("d:/uid.txt", $res);
            //   file_put_contents("d:/id.txt", $key);
            socket_write($ws->accept[$key], $res, strlen($res));
            break;
        }
    }
}

//给所有客服发信息
function send_to_all_service($data, $type, $ws) {
    $res = array(
        'msg' => $data,
        'type' => $type,
    );
    //  file_put_contents("d:/send_to_all_service1.txt", var_export($res, true));
    $res = json_encode($res);
    //   file_put_contents("d:/send_to_all_service2.txt", var_export($res, true));
    $res = $ws->encode($res);
    //  file_put_contents("d:/send_to_all_service3.txt", var_export($res, true));
    foreach ($ws->serviceLise as $key => $value) {
        @socket_write($ws->accept[$key], $res, strlen($res));
    }
}
