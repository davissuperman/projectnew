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