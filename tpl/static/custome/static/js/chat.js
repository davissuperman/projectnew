var ws = {};
var client_id = 0;
var userlist = {};
var touserfd = 0;
var touserid = 0;
var jq = $.noConflict();
jq(document).ready(function() {
    if (window.WebSocket || window.MozWebSocket) {
        ws = new WebSocket(webim.server);
        listenEvent();
    } else {
        WEB_SOCKET_SWF_LOCATION = "./static/flash-websocket/WebSocketMain.swf";
        jq.getScript("./static/flash-websocket/swfobject.js", function() {
            jq.getScript("./static/flash-websocket/web_socket.js", function() {
                ws = new WebSocket(webim.server);
                listenEvent();
            });
        });
    }
});

function listenEvent() {
    /**
     * 连接建立时触发
     */
    ws.onopen = function(e) {
        //必须的输入一个名称和一个图像才可以聊天
        if (u.uid == undefined || u.username == undefined) {
            alert('请先进行登录!');
            ws.close();
            return false;
        }
        //发送登录信息    
        msg = new Object();
        msg.type = 's';
        msg.cmd = 'login';
        msg.uid = u.uid;
        msg.name = u.username;
        msg.role = u.role;
        ws.send(jq.toJSON(msg));
    };
    //有消息到来时触发
    ws.onmessage = function(e) {
        var cmd = jq.evalJSON(e.data).cmd;
        if (cmd == 'login') {//客服登录后返回过来的
            client_id = jq.evalJSON(e.data).fd;
            //获取在线列表
            msg = new Object();
            msg.type = 's';
            msg.cmd = 'getOnline';
            ws.send(jq.toJSON(msg));
        }
        else if (cmd == 'getOnline') {//显示在线客服列表
            showOnlineList(e.data);
        }
        else if (cmd == 'newUser') {//有新客服登录时显示到自己的版面	
            showNewUser(e.data);
        }
        else if (cmd == 'fromMsg') {//有粉丝来信息时；
            showNewMsg(e.data);

        }
        else if (cmd == 'offline') {//有客服下线了
            var cid = jq.evalJSON(e.data).fd;
            delUser(cid);
        }
    };

    /**
     * 连接关闭事件
     */
    ws.onclose = function(e) {
        if (confirm("客服服务已关闭")) {
            location.href = '/index.php?g=Customer&m=Index&a=logout';
        }
    };

    /**
     * 异常事件
     */
    ws.onerror = function(e) {
        alert("异常:" + e.data);
        console.log("onerror");
    };
}

/**
 * 显示所有在线列表
 * @param data
 */
function showOnlineList(data) {
    var dataObj = jq.evalJSON(data);
    for (var i = 0; i < dataObj.list.length; i++) {
        var p = "<p class='daiP' id='uid" + dataObj.list[i].fd + "' onclick=\"selectuser('" + dataObj.list[i].fd + "','"+dataObj.list[i].uid  +"')\"><input type='radio' class='daiInp' name='uid' value='" + dataObj.list[i].uid + "'/><span>" + dataObj.list[i].name + "</span></p>";
        jq('.change').append(p);
    }

}

/**
 * 当有一个新客服登录时
 * @param userid
 */
function showNewUser(data) {
    var dataObj = jq.evalJSON(data);
    p = "<p class='daiP' id='uid" + dataObj.fd + "' onclick=\"selectuser('" + dataObj.fd + "','"+dataObj.uid +"')\"><input type='radio' class='daiInp' name='uid' value='" + dataObj.uid + "'/><span>" + dataObj.name + "</span></p>";
    jq('.change').append(p);
}
/**
 *  删除删除客服
 * @param {type} userid
 * @returns {undefined}
 */
function delUser(fd) {
    jq('#uid' + fd).remove();
}
//转接点击客服时
function selectuser(fd,uid){    
 touserid=uid; 
 touserfd=fd;
}
/**
 * 显示新消息
 */
function showNewMsg(data) {
    var dataObj = jq.evalJSON(data);
    var s = dataObj.s;
    var sss = jq('#' + dataObj.openid);
    if (s == 'all') {//如果和当前粉丝说话 就吧聊天数据显示到对话框        
        if (!sss.is('li')) {
            var p = "<li id='" + dataObj.openid + "' onclick=\"clickuser('" + dataObj.openid + "')\"  ><input type='radio' class='raInput' name='openid'   value='" + dataObj.openid + "' /><span class='userName'>" + dataObj.nickname + "</span></li>";
            jq('.user').append(p);
        }
        jq(".same").eq(0).clone().appendTo(jq(".dialogBox"));
        jq(".dialogBox").children().last().find(".fance").text(dataObj.data);
        jq(".dialogBox").children().last().find(".date").text(dataObj.t);
        jq(".dialogBox ").children().last().show();
        jq(".dialog").scrollTop(jq(".dialog")[0].scrollHeight);
    } else {
        if (!sss.is('li')) {
            var p = "<li id='" + dataObj.openid + "' onclick=\"clickuser('" + dataObj.openid + "')\"  ><input type='radio' class='raInput' name='openid'   value='" + dataObj.openid + "' /><span class='userName'>" + dataObj.nickname + "</span></li>";
            jq('.user').append(p);
        }
    }

}
//客服点击粉丝
function clickuser(openid) {
    jq('#' + openid).find(".raInput").attr("checked", true);
    jq('#openid').val(openid); //用户信息
    jq('#o_openid').val(openid); //用户信息
    jq.post("/index.php?g=Customer&m=Service&a=return_fance_info", {'openid': openid},
    function(data, status) {
        if (status == 'success') {
            jq(".dialogBox").children().remove();
            var info = jQuery.parseJSON(data);
            jq.each(info, function(k, v) {
                if (k == 'fance') {
                    jq('#infoname').text(v.nickname);
                    jq('#infoaddss').text(v.city);
                    var headimgurl = '/index.php?g=Customer&m=Service&a=showExternalPic&url=' + v.headimgurl;
                    jq('#infoimg').attr("src", headimgurl);
                    var gid = v.gid; //                           
                    jq('.op').each(function(i) {
                        if (gid == jq('.op').eq(i).val()) {
                            jq('#gid').val(gid);
                        }

                    });
                    jq('#inforemark').val(v.remark);
                }
                if (k == 'message') {
                    jq.each(v, function(mk, mv) {
                        if (mv.type == 1) {
                            jq(".same").eq(0).clone().appendTo(jq(".dialogBox"));
                            jq(".dialogBox").children().last().find(".fance").text(mv.message);
                            jq(".dialogBox").children().last().find(".date").text(mv.time);
                            jq(".dialogBox ").children().last().show();
                            jq(".dialog").scrollTop(jq(".dialog")[0].scrollHeight);
                        } else if (mv.type == 2) {
                            jq(".sameX").eq(0).clone().appendTo(jq(".dialogBox"));
                            jq(".dialogBox").children().last().find(".all").text(mv.message);
                            jq(".dialogBox").children().last().find(".date").text(mv.time);
                            jq(".dialogBox").children().last().show();
                            jq(".dialog").scrollTop(jq(".dialog")[0].scrollHeight);
                        }
                    })
                }
            });
        } else {
            alert('无法获得客户信息');
        }
    });
}
//给粉丝发信息
function sendinfo() {
    var openid = jq('#openid').val();
    if(openid==""){
        alert('请选择粉丝客户！');        
    }
    var sendinfo = jq('#sendinfo').val();
    jq('#sendinfo').val('');
    var myDate = new Date();
    var year = myDate.getFullYear();
    var month = myDate.getMonth() + 1;
    var day = myDate.getDate();
    var mytime = myDate.toLocaleTimeString();
    var time = year + "-" + month + "-" + day + " " + mytime;
    jq(".sameX").eq(0).clone().appendTo(jq(".dialogBox"));
    jq(".dialogBox").children().last().find(".all").text(sendinfo);
    jq(".dialogBox").children().last().find(".date").text(time);
    jq(".dialogBox").children().last().show();
    jq(".dialog").scrollTop(jq(".dialog")[0].scrollHeight);
    jq.post("/index.php?g=Customer&m=Service&a=send_fance_info", {'openid': openid, 'content': sendinfo},
    function(data, status) {
        if (status == 'success') {
            if (data != 'success') {
                jq('#sendinfo').val();
                alert('发送失败！请点击对应客户！');
            }
        }
    });
}
//回车给粉丝发信息
document.onkeydown = function(e) {
    var ev = document.all ? window.event : e;
    if (ev.keyCode == 13) {
        sendinfo();
    }
}
//修改客服
function info_submit() {
    var openid = jq('#openid').val();
    var inforemark = jq('#inforemark').val();
    var gid = jq("#gid").val();
    jq.post("/index.php?g=Customer&m=Service&a=fanceremark", {'openid': openid, 'inforemark': inforemark, 'gid': gid},
    function(data, status) {
        if (status == 'success' && data == 'success') {
            alert('修改成功！');
        } else {
            alert('修改失败！');
        }
    });
}
//客服直接点完成
function visitors_type() {
    var openid = jq('#openid').val();
    var visitors_type = jq(".rad:checked").val();
    if (!visitors_type) {
        return false;
    }
    jq.post("/index.php?g=Customer&m=Service&a=overmessage", {'openid': openid, 'visitors_type': visitors_type},
    function(data, status) {
        if (status == 'success' && data == 'success') {
            _party._manage();
        }
    });
    jq('#' + openid).remove();
    jq(".dialogBox").children().remove();
    jq("#infoname").text('');
    jq("#infoaddss").text('');
    jq("#infoimg").attr('src', '/tpl/static/custome/img/userImg.gif');
    jq('#what').val(0);
    jq('#openid').val('');
    jq(".change").hide();
}
//客服点击转接
function move() {
    jq(".change").show();
    jq(".userBox").scrollTop(jq(".userBox")[0].scrollHeight);
    jq(".userDetail").hide();
    jq(".reason").show();
}
//取消转接
function moveCancel() {
    jq(".change").hide();
    jq(".reason").hide();
    jq(".userDetail").show();
}
//确定转接
function movesubmit() {
    jq(".daiInp").attr("checked", false)
    jq(this).find(".daiInp").attr("checked", true);
    jq(".userLeft").children().css("opacity", "0.3")
    jq(".userImg").css("opacity", "0.3");
    jq(".userBox").children().css("opacity", "0.3");
    jq(".dialog").children().css("opacity", "0.3");
    jq("#what").attr('disabled', true);
    jq("#reason").css("opacity", "0.3");
    jq(".kefu").css("opacity", "0.3");
    jq(".sendPress").hide();
    jq(".okBox").css("margin-top", "35px");
    if (jq("#okX").length < 1) {
        jq("<input type='button' value='确定' id='okX' onclick='moveok()' class='surePress'/>").insertBefore(jq(".btnBoxX").find(".celPress"));
        jq(".btnBoxX").find(".surePress").first().hide();
    }
}
//转接完成
function moveok() {
    var openid = jq('#openid').val();
    var visitors_type = jq(".rad:checked").val();
    var what = jq('#what').val();
	var fd=touserfd;
	var nickname= jq("#"+openid+" .userName").text();
    if(touserid==""){
        alert('请选择客服');  return false;
    }
    if (!visitors_type) {
        return false;
    }
    jq.post("/index.php?g=Customer&m=Service&a=overmessage", {'openid': openid, 'visitors_type': visitors_type, 'what': what, 'uid': touserid, 'change': 'change'},
    function(data, status) {
        if (status == 'success' && data == 'success') {
             
              var msg = new Object();
                msg.cmd = 'message';
                msg.type = 's';
                msg.from = client_id;
                msg.to = fd;
                msg.openid=openid;
                msg.nickname = nickname;
                ws.send(jq.toJSON(msg)); 				
            _party._manage();
        } else {
            alert('请选择客服'); return false;
        }
    });
    jq('#' + openid).remove();
    jq(".dialogBox").children().remove();
    jq(".userLeft").children().css("opacity", "1")
    jq(".userImg").css("opacity", "1");
    jq(".userBox").children().css("opacity", "1");
    jq(".dialog").children().css("opacity", "1");
    jq(".kefu").css("opacity", "1");
    jq(".okBox").css("margin-top", "10px");
    jq("#infoname").text('');
    jq("#infoaddss").text('');
    jq("#infoimg").attr('src', '/tpl/static/custome/img/userImg.gif');
    jq(".sendPress").show();
    jq(".reason").hide();
    jq(".change").hide();
    jq(".userDetail").show();
    jq("#what").attr('disabled', false);
    jq("#reason").css("opacity", "1");
    jq(".kefu").css("opacity", "1");
    jq('#openid').val('');
    jq('#visitors_type').show();
    jq('#okX').remove();
    touserid="";
    touserfd="";
}
function backToSearch(){
  jq('#ordersRes').remove();
            jq('#backToSearch').remove();
            jq('#searchOrder').show();
}
jq(document).ready(function() {
 jq('#getOrders').click(function() {//订单
            var openid = jq('#o_openid').val();
            var mobile = jq('#o_mobile').val();
            if (openid.length < 1 && mobile.length < 1) {
                alert('微信ID或电话必须填写一个！');
                return;
            }
            jq.post("/index.php?g=Customer&m=Service&a=getOrders", {'openid': openid, 'mobile': mobile},
            function(data) {
                var state = new Array();
                state[1] = '待付款';
                state[4] = '已关闭';
                state[6] = '已完成';
                //var state = [1:'待付款',4:'已关闭',6:'已完成'];//状态列表待完善
                if (data.status == 0) {
                    var str = "<div class='show' id='ordersRes'>";
                    for (var i = 0; i < data.orders.length; i++) {
                        str += "<div class='order'>";
                        //订单号
                        str += "<p class='pBox' style='height:50px'>";
                        str += "<span class='state'>订单号：</span><br/>";
                        str += "<span class='stateTex' style='height:50px;'>" + data.orders[i].number + "</span>";
                        str += "</p>";
                        //订单金额
                        str += "<p class='pBox'>";
                        str += "<span class='state'>订单金额：</span>";
                        str += "<span class='money'>" + data.orders[i].oprice + "</span>";
                        str += "</p>";
                        //下单时间
                        str += "<p class='pBox'>";
                        str += "<span class='state'>下单时间：</span>";
                        str += "<span class='stateTex'>" + data.orders[i].time + "</span>";
                        str += "</p>";
                        str += "<p class='solds'>";
                        str += "</p>";
                        for (var j = 0; j < data.orders[i].products.length; j++) {
                            thisStatus = data.orders[i].sc;
                            str += "<div>";
                            str += "<img src='" + data.orders[i].products[j].pic + "' width='46' height='46' class='leftTu'/>";
                            str += "<div class='rightTex'>";
                            str += "<p class='orderTitle'>" + data.orders[i].products[j].name + "</p>";
                            str += "<p>";
                            str += "<span class='state'>订单状态：</span>";
                            str += "<span class='money'>" + state[thisStatus] + "</span>";
                            str += "</p>";
                            str += "</div>";
                            str += "<div class='clear'></div>";
                            str += "</div>";
                        }
                        str += "</div>";
                    }
                    str += "</div>";
                    str += "<p class='pressBox'><input type='button' value='返回' class='celPress' id='backToSearch'  onclick='backToSearch()'/></p>";
                    jq('#searchOrder').hide();
                    jq('#searchOrder').after(str);
                } else {
                    alert('此用户无订单记录');
                }
            }, 'json');
        });
		
         
});