/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var $ = function(id) {
    return document.getElementById(id);
};
var dataForWeixin = {
    appId: "",
    MsgImg: "http://www.baokulife.com/images/share_msg.png",
    TLImg: "http://www.baokulife.com/images/share.png",
    path: "",
    title: "投票",
    desc: "使用品息助手可以在朋友圈或聊天对话框发送更丰富的信息类型",
    fakeid: "",
    callback: function() {
        _$("./share.php?kind=app", "", "");
    }
};
        