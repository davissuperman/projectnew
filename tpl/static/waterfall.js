/**
 * @author 郑印
 * email zhengyin.name@gmail.com
 * qq: 928020070
 */
(function($){
	var pos = [];			//定位相关的数组
	var count = 0;			//统计加载已加载个数
	var listen = 0;			//监测本次加载是否完成的
	/**
	 * 给jQuery添加新的方法
	 */
	$.fn.extend({
		/**
		 * $(els).waterfall(function(){  });
 		 * @param {Object} fn  完成后的回调函数
		 */
		waterfall:function(moveStyle,fn){		
			var oParent = this.parent();	//瀑布流块父级
			var aPin = this;	//选中的瀑布流块 (下面以pin称呼它)
			//取得元素margin
			var iMarginX = parseInt(aPin.css('marginLeft'))+parseInt(aPin.css('marginRight'));
			var iMarginY = parseInt(aPin.css('marginTop'))+parseInt(aPin.css('marginBottom'));
			var iPinBaseW = aPin[0].offsetWidth+iMarginX;	 //pin的基础宽度
			var num = parseInt(oParent.width()/iPinBaseW);  //父级能容纳pin的个数
			oParent.css('width',iPinBaseW*num);  //计算并设置pin父级的宽度
			listen = aPin.size() - count;   //计算本次须加载的pin个数
			aPin.each(function(i){    //循环
				// if(i<count) return;	  //对已加载的pin不做处理
				$(this).css({position:'absolute'})
				if(i<num){ 	//小于num代表第一排的pin	
					$(this).css({
						left:i*iPinBaseW   //第一排位置，按索引排列   (i=0 => left=0)(i=1 => left = 1*iPinBaseW),
					})  
					pos[i] = $(this)[0].offsetHeight+iMarginY;  //往位置数组中压入值
					listen--;	//已完成加载，自减本次加载数
				}else{
					var minH = $().minH();	//计算列中的最小高度
					var minK = $().minK();  //计算最小列的key
					var minL = aPin.eq(minK).position().left; //计算最小列的left值 
					pos[minK] += $(this)[0].offsetHeight+iMarginY; //更新位置数组
					if(moveStyle === false){
						$(this).css({
							left:minL,
							top:minH
						})
						listen--;
						if(fn && listen<=0){
							fn();
						}
					}else{
						$(this).moveLoad(minL,minH,fn,moveStyle);		//运动加载
					}
				}
				count = i+1;		//更新统计数
			})
			oParent.css({
				height:$().maxH,
				overflow:'hidden'		
			});	//设置父级高度
		},
		/**
		 * 
 		 * @param int left   目标点left值
 		 * @param int top	   目标点top值
 		 * @param func fn	   加载完成后的回调函数	
 		 * @param int moveStyle  加载的风格	 
		 */
		moveLoad:function(left,top,fn,moveStyle){
			/**
			 * 默认,透明度变化
			 */
			if(moveStyle == undefined || moveStyle == ''){
				this.css({
					left:left,
					top:top,
					opacity:0
				}).animate({
					opacity:1
				},600,'',function(){
					listen--;
					if(fn && listen<=0){
						fn();
					}
				})
				return;
			}
			switch(moveStyle){
				case 1:			//从下往上
				this.css({
					left:left,
					top:Math.max.apply({},pos)+300
				}).animate({
					top:top
				},600,'',function(){
					listen--;
					if(fn && listen<=0){
						fn();
					}
				});
				break;
				case 2:		   //底部中心
				this.css({
					left:($(window).width()-this.width())/2,
					top:Math.max.apply({},pos)+300
				}).animate({
					left:left,
					top:top
				},600,'',function(){
					listen--;
					if(fn && listen<=0){
						fn();
					}
				});
				case 3:		   //左边
				this.css({
					left:0,
					top:Math.max.apply({},pos)+300
				}).animate({
					left:left,
					top:top
				},300,'',function(){
					listen--;
					if(fn && listen<=0){
						fn();
					}
				});
				case 4:		   //右边
				this.css({
					left:$(window).width(),
					top:Math.max.apply({},pos)+300
				}).animate({
					left:left,
					top:top
				},300,'',function(){
					listen--;
					if(fn && listen<=0){
						fn();
					}
				});
				
			}
		},
		minH:function(){
			return Math.min.apply({},pos);
		},
		minK:function(){
			var minH = Math.min.apply({},pos);
			for(var i in pos){
				if(pos[i] == minH){
					return i;
				}
			}
		},
		maxH:function(){
			return Math.max.apply({},pos);
		},
		finish:function(){
			return listen>0;
		}
	})
})(jQuery)