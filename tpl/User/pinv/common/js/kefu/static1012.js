Element.prototype.show=function(){this.style.display="block";};
Element.prototype.hide=function(){this.style.display="none";};
Element.prototype.center=function(top){
   this.style.left=(_system._scroll().x+_system._zero(_system._client().bw-this.offsetWidth)/2)+"px";
   this.style.top=(top?top:(/*_system._scroll().y+*/_system._zero(_system._client().bh-this.offsetHeight)/2))+"px";
};
var _system={
   _client:function(){
      return {w:document.documentElement.scrollWidth,h:document.documentElement.scrollHeight,bw:document.documentElement.clientWidth,bh:document.documentElement.clientHeight};
   },
   _scroll:function(){
      return {x:document.documentElement.scrollLeft?document.documentElement.scrollLeft:document.body.scrollLeft,y:document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop};
   },
   _cover:function(show){
      if(show){
	     $("cover").show();
	     $("cover").style.width=(this._client().bw>this._client().w?this._client().bw:this._client().w)+"px";
	     $("cover").style.height=(this._client().bh>this._client().h?this._client().bh:this._client().h)+"px";
	  }else{
	     $("cover").hide();
	  }
   },
   _zero:function(n){
      return n<0?0:n;
   },
};
