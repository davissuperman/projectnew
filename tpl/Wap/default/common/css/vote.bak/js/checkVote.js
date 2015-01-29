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

var _vote={
   _sel:"",
   _select:function(id){
      if($("option_"+id).src.indexOf("_yes")!=-1){
         $("option_"+id).src=$("option_"+id).src.replace(/_yes/,"_no");
	 this._sel=(","+this._sel+",").replace(","+id+",","");
      }else{
	 $("option_"+id).src=$("option_"+id).src.replace(/_no/,"_yes");
         if("sg"=="sg"){
	    if(this._sel!=""){$("option_"+this._sel).src=$("option_"+id).src.replace(/_yes/,"_no");}
	    this._sel=id.toString();
	 }else{
	    this._sel+=","+id;
	 }
      }
      this._sel=this._sel.replace(/\,{2,}/g,",").replace(/^\,/g,"").replace(/\,$/g,"");
   },
   _vote:function(){
      if(this._sel==""){_system._toast("你没有做出任何选择");return;}
      _$("/servlet/vote_vote","id=119518&kind=sg&sel="+this._sel,"请稍候","_vote._ok");
   },
   _ok:function(json){
      if(json.state=="0"){_system._toast("你的操作有误");return;}
      if(json.state=="1"){_system._toast("你已经投过票了");return;}
      _system._ok("投票成功",function(){
         $("vote_vote").innerHTML="投票成功";
         $("vote_vote").onclick=function(){_system._toast("你已经投过票了");};
         $("attch_list").innerHTML=json.html;
         $("vote_counts").innerHTML=json.counts;
	 if($("test_result")){$("test_result").show();}
      });
   }
};