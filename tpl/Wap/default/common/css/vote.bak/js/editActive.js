var $=function(id){return document.getElementById(id);};
var dataForWeixin={
   appId:"",
   MsgImg:"http://img1.messagehelper.net/images/share_msg.png",
   TLImg:"http://img1.messagehelper.net/images/share.png",
   path:"",
   title:"信息助手",
   desc:"使用信息助手可以在朋友圈或聊天对话框发送更丰富的信息类型",
   fakeid:"",
   callback:function(){_$("/servlet/share?kind=app&id=0","","");}
};

var _party={
   _join:function(show){
      if(show){
         _system._cover(true);
         $("party_join").show();
         $("party_join").center();
         window.onresize=function(){_system._cover(true);$("party_join").center();};
         $("cover").onclick=function(){_party._join();};
      }else{
         _system._cover();
         $("party_join").hide();
	 $("cover").onclick=null;
	 window.onresize=null;
      }
   },
   _manage:function(show){
      if(show){
         _system._cover(true);
         $("party_pass").show();
         $("party_pass").center();
         window.onresize=function(){_system._cover(true);$("party_pass").center();};
         $("cover").onclick=function(){_party._manage();};
      }else{
         _system._cover();
         $("party_pass").hide();
         $("pass_pass").value="";
	 $("cover").onclick=null;
	 window.onresize=null;
      }
   }
};
var _join={
   _post:function(){
      var _nick=$("join_nick").value.trim(),_phone=$("join_phone").value.trim();
      if(_nick==""){_system._toast("请报上你的大名");return;}
      if(_nick.len()>20){_system._toast("大名要在20字节以内");return;}
      if(_phone==""){_system._toast("请录入你的手机号码");return;}
      if(_phone.match(/^\d{7,11}$/)==null){_system._toast("手机号输入有误");return;}
      _party._join();
      _$("/servlet/party_join","id=104435&nick="+_nick.encode()+"&phone="+_phone,"请稍候","_join._ok");
   },
   _ok:function(json){
      if(json.state=="0"){_system._toast("你填写的内容有问题");return;}
      if(json.state=="1"){_system._toast("你已经报过名了");return;}
      _system._ok("报名成功",function(){
         $("join_post").innerHTML="你已经报过名了";$("join_post").onclick=function(){_system._toast("你已经报过名了");};
         if($("record_nodata")){$("record_nodata").parentNode.removeChild($("record_nodata"));}
         $("attch_list").innerHTML="<div class='attch_item'><div class='left'>"+$("join_nick").value+"</div><div class='right'>刚刚</div><div class='clear'></div></div>"+$("attch_list").innerHTML;
         $("party_counts").innerHTML=parseInt($("party_counts").innerHTML)+1;
      });
   }
};
var _show={
   _post:function(){
      _$("/servlet/party_record","id=104435","加载中","_show._ok");
   },
   _ok:function(json){
      if(json.state=="0"){_system._toast("你的操作有误");return;}
      $("attch_list").innerHTML=json.html;
      $("party_counts").innerHTML=json.counts;
   }
};
var _manage={
   _post:function(){
      var _pass=$("pass_pass").value.trim();
      if(_pass==""){_system._toast("请输入管理密码");return;}
      if(_pass.len()<6 || _pass.len()>16 ){_system._toast("密码输入有误");return;}
      _party._manage();
      _$("/servlet/party_manage","id=104435&pass="+_pass.encode(),"请稍候","_manage._ok");
   },
   _ok:function(json){
      if(json.state=="0"){_system._toast("密码输入有误");return;}
      $("attch_list").innerHTML=json.html;
      $("party_counts").innerHTML=json.counts;
   }
};
(function(){
   dataForWeixin.MsgImg="http://img1.messagehelper.net/images/party_msg.png";
   dataForWeixin.TLImg="http://img1.messagehelper.net/images/party.png";
   dataForWeixin.path="party/104435";
   dataForWeixin.title="1231312313";
   dataForWeixin.desc="12313123131231";
   dataForWeixin.callback=function(){_$("/servlet/share?kind=party&id=104435","","");};
})();

var _fromCode="http://get-party-from-area2.info/detail.jsp?r=";

var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F306d85a63ad5a56e3caabd31a5356c1b' type='text/javascript'%3E%3C/script%3E"));
