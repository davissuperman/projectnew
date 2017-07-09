;(function($){ 
	$.extend({
		logout:function(btn) {
			var windowObj = window.parent || window;
			var pathname = windowObj.location.pathname;
			
			for ( var p in windowObj.appConfig.authAppMap) {
				this.logoutApp(p);
			}
			
			//处理sso加入的url后缀
			pathname = pathname.split(";")[0];
			
			var host = 'http://' + windowObj.location.host + pathname;

			var param = {
				url : host+'logout',
				callType : 'get',
				contentType : 'application/json',
				dataType : 'json',
				data : {}
			};
			
			$.callApi(param, function(response) {
				//刷新页面
				windowObj.location.href = response.loginUrl;
			}, function(XMLHttpRequest, textStatus, errorThrown) {
				console.dir([ "Error", errorThrown ]);
			});
		},
		callApi : function(param, callback, errorback) {
			var defaultCallback = function(response){
				console.dir(['response', response]);
			}
			var defaultErrorback = function(XMLHttpRequest, textStatus, errorThrown) {
				$.Pop.alerts('后端异常，请联系管理员');
			};
			if (param.dataType.toUpperCase() == 'JSONP') {
				$.ajax({
					url : param.url,
					type : param.callType,
					dataType : param.dataType,
					data : param.data,
					contentType : param.contentType || "application/json",
					callback : 'callback',
					jsonp : "callback",
					success : callback || defaultCallback,
					error : errorback || defaultErrorback
				});
			} else {
				$.ajax({
					url : param.url,
					type : param.callType,
					dataType : param.dataType,
					data : param.data,
					contentType : param.contentType || "application/json",
					success : callback || defaultCallback,
					error : errorback || defaultErrorback
				});
				
			}
		},
		getDateStr:function(addDayCount) {
    		var dd = new Date();
    		dd.setDate(dd.getDate()+addDayCount);//获取addDayCount天后的日期
    		var y = dd.getFullYear();
   			var m = dd.getMonth()+1;//获取当前月份的日期
    		var d = dd.getDate();
			
			var hours = dd.getHours();       //获取当前小时数(0-23)
			var minutes = dd.getMinutes();     //获取当前分钟数(0-59)
			var seconds = dd.getSeconds();     //获取当前秒数(0-59)
			
			if(m < 10){
				m = '0' + m;
			}
			if(d < 10){
				d = '0' + d;
			}
			if(hours < 10){
				hours = '0' + hours;
			}
			if(minutes < 10){
				minutes = '0' + minutes;
			}
			if(seconds < 10){
				seconds = '0' + seconds;
			}
			
    		return y+"-"+m+"-"+d + ' '+hours + ':'+minutes + ':'+seconds;
		},
		
		Pop:{
			alerts:function(text,hashSrc){
	            var btn ='<div class="btn-box"><a href="javascript:void(0)" id="confirms" class="btn btn-blue">确认</a></div>';
	            this.createDiv(text,btn,'',hashSrc);
	        },
	        confirms:function(text,fn){
	            var btn ='<div class="btn-box"><a href="javascript:void(0)" id="confirms" class="btn btn-blue">确认</a><a href="javascript:void(0)" id="cancel" class="btn btn-gray">取消</a></div>';
	            this.createDiv(text,btn,fn);
	        },
	        createDiv:function(text,btn,fn,hashSrc){
	        	var oBg=document.createElement('div');
			    var oDiv=document.createElement('div');
			    oBg.className='pop-bg';
			    oDiv.className="pop-box";
			    
	        	var text = text || '';
	            var windowObj = window.parent || window;
	            var htmlChild =windowObj.document.getElementsByTagName('body')[0];
	            var html = '';
	            if(fn){
	            	html += '<div class="head"><i class="icons-big icons-big-confirm"></i>信息</div>';
	            	html += '<div class="pop-text confirms-text"><div class="text">'+text+'</div>'+btn+'</div>';
	            }else{
	            	html += '<div class="head"><i class="icons-big icons-big-alert"></i>信息</div>';
	            	html += '<div class="pop-text alerts-text"><div class="text">'+text+'</div>'+btn+'</div>';;
	            }
	            
	            oDiv.innerHTML= html;
	            htmlChild.appendChild(oBg);
	            htmlChild.appendChild(oDiv);
	            var oDivW = oDiv.offsetWidth/2;
	            var oDivH = oDiv.offsetHeight/2;
	            var windowH = (htmlChild.offsetHeight || window.outerHeight)/3;
	            //var windowH = htmlChild.offsetHeight/3 === 0 ?400 : htmlChild.offsetHeight/3;
	            var windowW = htmlChild.offsetWidth/2;

	            //oDiv.style.left = windowW - oDivW+'px';//不需要计算，固定宽度
	           // oDiv.style.top = windowH - oDivH+'px';  
	            //console.log(oDiv.childNodes[1].childNodes[0]);
	            oDiv.childNodes[1].childNodes[1].childNodes[0].onclick=function(){
	                htmlChild.removeChild(oBg);
	                htmlChild.removeChild(oDiv);
	                if(hashSrc){
	                	if(hashSrc == 'history_back'){//返回历史记录上一页
	                		window.history.back();
	                	}else{//返回指定的url地址
	                		location.hash = hashSrc;
	                	}
	                }
	                if(fn){fn();}
	            };
	            if(fn){
	                oDiv.childNodes[1].childNodes[1].childNodes[1].onclick=function(){
	                    htmlChild.removeChild(oBg);
	                    htmlChild.removeChild(oDiv);
	                };
	            }
	        }

		    
		},
		layerLoading:{
			show:function(){
				this._createLoadingHtml();
			},
			hide:function(){
				if(window.parent){
					$('#layer-loading', parent.document).remove(); 
				}else{
					$("#layer-loading").remove();
				}
			},
			_createLoadingHtml:function(){
				var html = '<div class="layer-loading" id="layer-loading">';
				html += '<div class="loading-bg"></div>';
				html += '<div class="loading-con"></div>';
				html += '</div>';
				
				var windowObj = window.parent || window;
	            var htmlChild =windowObj.document.getElementsByTagName('body')[0];
	            if($(htmlChild).find("#layer-loading").length == 0){
	            	$(htmlChild).append(html);
	            }
			}
		}
		
	}); 
})($);

