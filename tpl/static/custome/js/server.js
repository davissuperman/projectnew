(function() {
    var jq = $.noConflict();
    function servicelist(date) {//客服列表
        var member = jq('#snum').text();
        var date = JSON.parse(date);
        if (date.number) {
            if (date.uid != member) {//如果客服列表里面存在就不添加
                service = 0;
                jq(".daiP").each(function(i) {
                    if (jq(".daiP").eq(i).find(".daiInp").val() == date.uid)
                    {
                        service = 1;
                        return false;
                    }

                });
                if (service == 0) {
                    jq(".daiP").eq(0).clone().appendTo(jq(".change"));
                    jq(".daiP").last().find(".daiInp").val(date.number);
                    jq(".daiP").last().find("span").text(date.name);
                    jq(".daiP").last().show();
                }             
            }
        } else {//如果客服列表不存在就添加
            jq(".daiP").each(function(i) {//遍历是否里面存在
                if (jq(".daiP").eq(i).find(".daiInp").val() == date.uid) {
                    jq(".daiP").eq(i).remove();
                }
            });
        }
    }
    function fancelist(date) {//客户列表
        var date = JSON.parse(date);
        var member = jq('#snum').text();
        if (date.uid == member) {//
            if (date.s == 1) {//呼叫客服把粉丝放入队列
                var fance = 0;
                jq(".user li").each(function(i) {
                    if (jq(".user li").eq(i).find(".raInput").val() == date.openid)
                    {
                        fance = 1;
                        return false;
                    }                   
                });
                if (fance == 0) {
                    jq(".user li").first().clone().appendTo(".user");
                    jq(".user li").last().find(".raInput").val(date.openid);
                    jq(".user li").last().find(".userName").text('用户昵称:' + date.nickname);
                    jq(".user li").last().show();
                    jq(".user li").last().css("background", "#fff");
                    jq(".user li").last().find(".raInput").attr("checked", false);
                }

            } else if (date.s == 3) {// 如果已经到队列了 提醒这个用户有新消息 
                var startus = 0;
                jq(".raInput").each(function(i) {
                    if (jq(".raInput").eq(i).val() == date.openid) {
                        startus = 1;
                        jq(".raInput").eq(i).next().css("color", 'red');
                        return false;
                    }
                });
                if (startus == 0) {
                    var fance = 0;
                    jq(".user li").each(function(i) {
                        if (jq(".user li").eq(i).find(".raInput").val() == date.openid)
                        {
                            fance = 1;
                            return false;
                        }
                        ;
                    });
                    if (fance == 0) {
                        jq(".user li").first().clone().appendTo(".user");
                        jq(".user li").last().find(".raInput").val(date.openid);
                        jq(".user li").last().find(".userName").text('用户昵称:' + date.nickname);
                        jq(".user li").last().show();
                        jq(".user li").last().css("background", "#fff");
                        jq(".user li").last().find(".raInput").attr("checked", false);
                    }
                }
            } else if (date.s == 500) {
                jq(".same").eq(0).clone().appendTo(jq(".dialogBox"));
                jq(".dialogBox").children().last().find(".fance").text(date.info);
                jq(".dialogBox").children().last().find(".date").text(date.t);
                jq(".dialogBox ").children().last().show();
                jq(".dialog").scrollTop(jq(".dialog")[0].scrollHeight);

            }
        }
    }
    ///////////////
    jq(document).ready(function() {
        jq(window).bind('beforeunload', function() {
            return  "请不要进行刷新操作！退出要点客服信息的退出按钮!";
        });
        jq("#recancel").click(function() {
            jq(".userLeft").children().css("opacity", "1")
            jq(".userImg").css("opacity", "1");
            jq(".userBox").children().css("opacity", "1");
            jq(".dialog").children().css("opacity", "1");
            jq("#what").attr('disabled', false);
            jq("#reason").css("opacity", "1");
            jq(".kefu").css("opacity", "1");
            jq(".okBox").css("margin-top", "10px");
            jq(".sendPress").show();

        });
        jq(".user li").live("click", function() {
            jq(".user li").css("background", "")
            jq(this).css("background", "#F6F6F6");
            jq(".user li").addClass("gray");
            //jq(".raInput").attr("checked", false);
            jq(this).find(".raInput").attr("checked", true);
        });
        jq(".box").css("margin-top", (jq(window).height() - 580) / 2); //滚动居中
        jq(".kefu").click(function() {//转接客服            
            jq(".change").show();
            jq(".userBox").scrollTop(jq(".userBox")[0].scrollHeight);
            jq(".userDetail").hide();
            jq(".reason").show();
        });
        var mm = 0;
        jq(".daiP").find(".daiInp").live("click", function() {
            mm = jq(this).val();
        });
        jq("#reason").click(function() {//提交转接
            var uid = mm;
            if (uid == 0) {
                alert('请选择客服');
                return false;
            }
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
                jq("<input type='button' value='确定' id='okX' class='surePress'/>").insertBefore(jq(".btnBoxX").find(".celPress"));
                jq(".btnBoxX").find(".surePress").first().hide();
            }

        });

        jq("#recancel").click(function() {//转接取消
            jq(".change").hide();
            jq(".reason").hide();
            jq(".userDetail").show();
        })
        jq(".area").click(function() {
            jq(this).val("");
        });
//socket start
        var host = "ws://192.168.100.49:8080";
        var member = jq('#snum').text();
        var sname = jq('#sname').text();
        var socket = new WebSocket(host);
        socket.onopen = function(msg) {
            if (this.readyState) {
                socket.send('{"number":' + '"' + member + '" ,' + '"uid":' + '"' + member + '" ,' + '"name":' + '"' + sname + '"' + ',"role":"s"' + "}");
                jq('#sstatic').text('在线');
            } else {
                jq('#sstatic').text('失败');
            }
        };
        socket.onerror = function(evt) {//服务器异常
            alert('服务器关闭');
        };
        socket.onmessage = function(evt) {
            // var data = evt.data;
            var data = JSON.parse(evt.data);
            switch (data.type) {
                case 's':
                    servicelist(data.msg);
                    break;
                case 'f':
                    fancelist(data.msg);
                    break;
            }
            date = '{}';
        };
        socket.onclose = function(msg) {
            alert('服务器关闭');
            socket.close();
            socket = null;
        };
//socketend
        jq("#logout").click(function() {//客服退出登录
            var member = jq('#snum').text();
            socket.send('{"uid":' + '"' + member + '" ,' + '"name":' + '"' + sname + '"' + ',"role":"s"' + "}");
            window.location.href = '/index.php?g=Customer&m=Index&a=logout';

        });
        jq(".user >li").live("click", function() {//客服点击对应粉丝 
            var openid = jq(this).find(".raInput").val();
            jq(this).find(".raInput").attr("checked", true);
            jq('#openid').val(openid); //openid 给发信息的框
            //jq('#infoid').val(openid); //用户信息
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
        });
        jq(".area").keyup(function(event) {//enter 给客户发信息
            if (event.keyCode == 13)
            {
                var openid = jq('#openid').val();
                var sendinfo = jq('#sendinfo').val();
                jq('#sendinfo').val('');
                var myDate = new Date();
                var year = myDate.getFullYear();
                var month = myDate.getMonth() + 1;
                var day = myDate.getDate();
                var mytime = myDate.toLocaleTimeString();
                var time = year + "-" + month + "-" + day + " " + mytime;
                jq(".sameX").clone().appendTo(jq(".dialogBox"));
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
        });
        jq("#send_info .sendBtn").click(function() {//客服给粉丝发信息            
            var openid = jq('#openid').val();
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
        });
        jq("#info_submit").click(function() {//修改备注信息
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
        });
        jq("#okX").live("click", function() {//转接后点击完成
            var openid = jq('#openid').val();
            var visitors_type = jq(".rad:checked").val();
            var what = jq('#what').val();
            var uid = mm;
            if (uid == 0) {
                alert('请选择客服');
                return false;
            }
            if (!visitors_type) {
                return false;
            }
            jq.post("/index.php?g=Customer&m=Service&a=overmessage", {'openid': openid, 'visitors_type': visitors_type, 'what': what, 'uid': uid, 'change': 'change'},
            function(data, status) {
                if (status == 'success' && data == 'success') {
                    _party._manage();
                }
            });
            jq(".user li").each(function(i) {
                if (jq(".user li").eq(i).find(".raInput").val() == openid)
                {
                    jq(".user li").eq(i).remove();
                }
            });
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
            mm = 0;
            jq('#openid').val('');
            jq('#visitors_type').show();
            jq('#okX').hide();
            

        });
        jq("#visitors_type").click(function() {//直接点完成             
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
            jq(".user li").each(function(i) {
                if (jq(".user li").eq(i).find(".raInput").val() == openid)
                {
                    jq(".user li").eq(i).remove();
                }
            });
            jq(".dialogBox").children().remove();
            jq("#infoname").text('');
            jq("#infoaddss").text('');
            jq("#infoimg").attr('src', '/tpl/static/custome/img/userImg.gif');
            jq('#what').val(0);
            jq('#openid').val('');
            jq(".change").hide();

        });
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
                    str += "<p class='pressBox'><input type='button' value='返回' class='celPress' id='backToSearch'/></p>";
                    jq('#searchOrder').hide();
                    jq('#searchOrder').after(str);
                } else {
                    alert('此用户无订单记录');
                }
            }, 'json');
        });
        jq('#backToSearch').live('click', function() {
            jq('#ordersRes').remove();
            jq('#backToSearch').remove();
            jq('#searchOrder').show();
        });
        $('#getOrders').click(function() {
            var openid = $('#o_openid').val();
            var mobile = $('#o_mobile').val();
            if (openid.length < 1 && mobile.length < 1) {
                alert('微信ID或电话必须填写一个！');
                return;
            }
            $.post("/index.php?g=Customer&m=Main&a=getOrders", {'openid': openid, 'mobile': mobile},
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
                    str += "<p class='pressBox'><input type='button' value='返回' class='celPress' id='backToSearch'/></p>";
                    $('#searchOrder').hide();
                    $('#searchOrder').after(str);
                } else {
                    alert('此用户无订单记录');
                }
            }, 'json');
        });
        $('#backToSearch').live('click', function() {
            $('#ordersRes').remove();
            $('#backToSearch').remove();
            $('#searchOrder').show();
        });
    });
}).call(this);