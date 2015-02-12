var cur_url = $("#cur-url").val();
var V2, V3, V4;
	var MF = cur_url + "/year/images/p2/";
	var V6;
	var V7 = new Array(64);
	var V8 = new Array("场景1");
	var C0, C0L1S5, C0L1S4, C0L1S3, C0L1S2, C0L1S1, C0L1S0;
	var V9 = 1;
	var V11 = new Date();
	var V12 = (Math.PI * 2) / 360,
	V13 = 0;
	var V14, V15 = -1,
	V16 = -1,
	V17 = -1,
	V18 = 0,
	V19 = 0,
	V20 = 1,
	V21 = 64;
	var V22 = "none",
	V23 = "block",
	V24 = "px",
	V25 = "";
	var IE = -1,
	V26 = 0;
	var V27 = true,
	V28 = false;
	function F2(e) {
		e = F3(e);
		if (e != null) {
			if (e.stopPropagation) {
				e.stopPropagation();
			}
			if (e.preventDefault) {
				e.preventDefault();
			}
			e.cancelBubble = true;
			e.returnValue = false;
		}
		return false;
	}
	var V29 = null;
	function F4(e) {
		if (window.event) {
			e = window.event;
		}
		if (e.touches) {
			if (e.touches.length == 0) {
				return V29;
			}
			e = e.touches[0];
		}
		var Mouse = new Object();
		Mouse.X = e.clientX;
		Mouse.Y = e.clientY;
		var Scale = Math.min(V30 / 580, V31 / 200);
		Mouse.X = (Mouse.X - (V30 - (Scale * 580)) / 2) / Scale;
		Mouse.Y = (Mouse.Y - (V31 - (Scale * 200)) / 2) / Scale;
		V29 = Mouse;
		return Mouse;
	}
	function F3(e) {
		if (window.event) {
			return window.event;
		} else {
			return e;
		}
	}
	function F5(Angle) {
		return 90 * Math.round((Angle - 45) / 90);
	}
	function F6(Pos) {
		if (Pos == 0) {
			return 0;
		}
		if ((Pos /= .5) == 2) {
			return 1;
		}
		var p = (.3 * 1.5);
		var s = p / (2 * Math.PI) * Math.asin(1);
		if (Pos < 1) {
			return - .5 * (Math.pow(2, 10 * (Pos -= 1)) * Math.sin((Pos * 1 - s) * (2 * Math.PI) / p));
		}
		return Math.pow(2, -10 * (Pos -= 1)) * Math.sin((Pos * 1 - s) * (2 * Math.PI) / p) * .5 + 1;
	}
	function F7(Pos) {
		if (Pos == 0) {
			return 0;
		}
		if (Pos == 1) {
			return 1;
		}
		var p = .3;
		var s = p / (2 * Math.PI) * Math.asin(1);
		return 1 * Math.pow(2, -10 * Pos) * Math.sin((Pos * 1 - s) * (2 * Math.PI) / p) + 1;
	}
	var V30 = 0,
	V31 = 0;
	function F9() {
		if (parent.document.documentElement.clientWidth > 0) {
			return parent.document.documentElement.clientWidth;
		} else {
			return F10(parent.window.innerWidth, parent.document.body.clientWidth);
		}
	}
	function F11() {
		if (parent.document.documentElement.clientHeight > 0) {
			return parent.document.documentElement.clientHeight;
		} else {
			return F10(parent.window.innerHeight, parent.document.body.clientHeight);
		}
	}
	function F10(Value1, Value2) {
		var Value = 0;
		if (!isNaN(Value1) && Value1 > Value) {
			Value = Value1;
		}
		if (!isNaN(Value2) && Value2 > Value) {
			Value = Value2;
		}
		return Value;
	}
	function F12() {
		V30 = F9();
		V31 = F11();
		if (!V28) {
			V28 = V30 != V16 || V31 != V17;
		}
		V16 = V30;
		V17 = V31;
	}
	function F13(Id) {
		return document.getElementById(Id);
	}
	var V32Details;
	var V33Details;
	function UpdateVideoJS(Id, Position, Duration, Volume, Buffer) {
		var Details = window[Id + "Details"];
		Details.Position = Position;
		Details.Duration = Duration;
		Details.Volume = Volume;
	}
	function F14() {
		C0L1S5.src = MF + "heng.png";
		C0L1S4.src = MF + "img02.png";
		C0L1S3.src = MF + "yingzhang.png";
		C0L1S2.src = MF + "zuo.png";
		C0L1S0.src = MF + "you.png";
	}
	var V34 = new Array();
	var V35 = new Array();
	function F15(Sound) {
		var i = 0;
		for (var i; i < V34.length; i++) {
			if (V34[i] == Sound) {
				return V35[i];
			}
		}
		return null;
	}
	function F16(Name, Details) {
		V34.push(Name);
		V35.push(Details);
	}
	function F17(Sound, Url, Wait) {
		var Details = F15(Sound);
		Details.Timer = window.setTimeout("F18('" + Sound + "');", Wait);
		if (Details.Element.addEventListener) {
			Details.Element.addEventListener('loadeddata', new
			function() {
				F18(Sound);
			},
			false);
			Details.Element.addEventListener('error', new
			function() {
				F18(Sound);
			},
			false);
		}
		if (Url == null) {
			Details.Element.src = MF + 'Sound_' + Sound + '.mp3';
		} else {
			Details.Element.src = Url;
		}
	}
	function F18(Sound) {
		var Details = F15(Sound);
		if (Details.Timer > 0) {
			window.clearTimeout(Details.Timer);
			V26++;
			if (Details.Element.removeEventListener) {
				Details.Element.removeEventListener('loadeddata', F18, false);
				Details.Element.removeEventListener('error', F18, false);
			}
		}
	};
	function SoundPlay(Sound, Loop) {
		var Details = F15(Sound);
		if (Details.Element.loop != undefined) {
			Details.Element.loop = Loop;
		} else {
			if (Details.Element.addEventListener) {
				if (Loop) {
					Details.Element.addEventListener('ended', V36, false);
				} else {
					Details.Element.removeEventListener('ended', V36, false);
				}
			}
		}
		try {
			Details.Element.currentTime = 0;
		} catch(e) {}
		Details.Element.play();
	}
	function SoundStop(Sound) {
		var Details = F15(Sound);
		if (Details.Element.loop == undefined && Details.Element.removeEventListener) {
			Details.Element.removeEventListener('ended', V36, false);
		} else {
			Details.Element.loop = false;
		}
		Details.Element.pause();
	}
	function SoundPause(Sound) {
		var Details = F15(Sound);
		Details.Element.pause();
	}
	function SoundResume(Sound) {
		var Details = F15(Sound);
		Details.Element.play();
	}
	function F19() {
		V2 = F13("L");
		V37 = F13("LB");
		V4 = F13("LP");
		C0 = F13("C0");
		V6 = C0;
		C0L1S5 = F13("C0L1S5");
		C0L1S4 = F13("C0L1S4");
		C0L1S3 = F13("C0L1S3");
		C0L1S2 = F13("C0L1S2");
		C0L1S1 = F13("C0L1S1");
		C0L1S0 = F13("C0L1S0");
		V32Details = new
		function F20() {
			this.Volume = 100;
			this.Position = 0;
			this.Duration = 0;
			this.Timer = 0;
			this.Buffer = null;
			this.Source = null;
			this.Gain = null;
			this.Start = new Date();
			Pause = 0;
			this.Element = F13("Sound_luoxia");
		};
		F16("luoxia", V32Details);
		V33Details = new
		function F20() {
			this.Volume = 100;
			this.Position = 0;
			this.Duration = 0;
			this.Timer = 0;
			this.Buffer = null;
			this.Source = null;
			this.Gain = null;
			this.Start = new Date();
			Pause = 0;
			this.Element = F13("Sound_gundong");
		};
		F16("gundong", V33Details);
		V16 = -1;
		V17 = -1;
		F12();
		V14 = window.setTimeout("F21();", 100);
	}
	function F23(Type, Div, X, Y, Width, Height, Opacity, Angle, FontSize, BorderWidth) {
		if (Opacity == 0) {
			Div.style.display = V22;
			return;
		}
		V25 = "";
		V9 = Math.min(V30 / 580, V31 / 200);
		X *= V9;
		Y *= V9;
		FontSize *= V9;
		BorderWidth *= V9;
		Width *= V9;
		Height *= V9;
		if (FontSize > 0) {
			V25 += "font-size:" + FontSize + "px;";
		}
		if (BorderWidth > 0) {
			V25 += "border-width:" + Math.round(BorderWidth) + "px;";
			Div.strokeWeight = BorderWidth.toString();
		}
		if (Div.style.cssText != V25) {
			Div.style.cssText = V25;
		}
		var NewLeft = X - (Width / 2),
		NewTop = Y - (Height / 2);
		Div.style.left = NewLeft.toFixed(2) + V24;
		Div.style.top = NewTop.toFixed(2) + V24;
		Div.style.width = Width.toFixed(2) + V24;
		Div.style.height = Height.toFixed(2) + V24;
		Div.style.display = V23;
	}
	function F24(Type, Div) {
		V25 = "display:none;";
		if (Div.style.cssText != V25) {
			Div.style.cssText = V25;
		}
	}
	function F25(Index) {
		V13 = 0;
	}
	function F26() {
		if (V19 < V20 - 1) {
			F25(V19 + 1);
		} else {
			F25(0);
		}
	}
	function F27(Div, Clip) {
		if (Clip) {
			var Scale = Math.min(V30 / 580, V31 / 200);
			Div.style.cssText = "left:" + ((V30 - (Scale * 580)) / 2) + "px;top:" + ((V31 - (Scale * 200)) / 2) + "px;width:" + (Scale * 580) + "px;height:" + (Scale * 200) + "px;display:block;overflow:hidden;";
		} else {
			Div.style.cssText = "left:0px;top:0px;width:" + V30 + "px;height:" + V31 + "px;display:block;overflow:hidden;";
		}
	}
	function F21() {
		F17("luoxia", null, 936);
		F17("gundong", null, 1646);
		F28(MF + "heng.png", true, false);
		F28(MF + "img02.png", true, false);
		F28(MF + "yingzhang.png", true, false);
		F28(MF + "zuo.png", true, false);
		F28(MF + "you.png", true, false);
		V14 = window.setTimeout("F29();", 15);
	}
	function F28(Filename, Wait, BackgroundImage) {
		var I = document.createElement("img");
		if (IE > 8) {
			I.style.cssText = "position:absolute;top:-1px;width:1px;height:1px;";
			document.getElementsByTagName("body")[0].appendChild(I);
		}
		if (Wait) {
			I.onload = function() {
				V26++;
			};
			I.onerror = function() {
				V26++;
			};
		}
		I.src = Filename;
	}
	function F29() {
		var Percent = Math.round(Math.min(100, (V26 * 100) / 7));
		V37.style.width = Percent + "%";
		V4.innerHTML = Percent + "%";
		V2.style.display = V23;
		V2.style.left = Math.round((V30 - L.clientWidth) / 2) + V24;
		V2.style.top = Math.round((V31 - L.clientHeight) / 2) + V24;
		if (V26 >= 7) {
			V2.style.display = V22;
			F14();
			V11 = new Date();
			V14 = window.setTimeout("F22();", 15);
		} else {
			V14 = window.setTimeout("F29();", 15);
		}
	}
	function F30() {
		F27(V6, true);
		V28 = false;
	}
	function F22() {
		if (V28) {
			F30();
		}
		var CTime = new Date();
		var TSpan = CTime.getTime() - V11.getTime();
		V11 = CTime;
		var SpanGap = Math.min(55, Math.max(1, 55 - TSpan + V18));
		V18 = SpanGap;
		var Span = (TSpan / 1000) * 12;
		if (Span > 1) {
			Span = 1;
		};
		if (V27) {
			V13 += Span;
		}
		if (V13 > V21) {
			if (V27) {
				V27 = false;
			}
		}
		var Pos = 0,
		NX, NY, NA;
		switch (V19) {
		case 0:
			if (V13 < 17) {
				F24(1, C0L1S5);
			}
			if (V13 >= 17) {
				F23(1, C0L1S5, 297, 112, 495, 122, 100, 0, 0, 0);
			}
			if (V13 < 17) {
				F24(1, C0L1S4);
			}
			if (V13 >= 17) {
				F23(1, C0L1S4, 286, 105, 396, 89, 100, 0, 0, 0);
			}
			if (V13 < 50) {
				F24(1, C0L1S3);
			}
			if (V13 >= 50) {
				F23(1, C0L1S3, 482.81, 90.15, 20, 27, 100, 0, 0, 0);
			}
			if (V13 >= 0 && V13 < 16) {
				Pos = (V13 - 0) / 16;
				Pos = F6(Pos);
				NX = 49 + (Pos * 3);
				NY = -88 + (Pos * 203);
				F23(1, C0L1S2, NX, NY, 34, 151, 100, 0, 0, 0);
			}
			if (V13 >= 16) {
				F23(1, C0L1S2, 52, 115, 34, 151, 100, 0, 0, 0);
			}
			if (V13 < 17) {
				F24(1, C0L1S1);
			}
			if (V13 >= 17 && V13 < 60) {
				Pos = (V13 - 17) / 43;
				NX = 320.89 + (Pos * 445.31);
				NY = 109.47 + (Pos * 0.03);
				F23(1, C0L1S1, NX, NY, 480 + (Pos * -22), 147, 100, 0, 0, 0);
			}
			if (V13 >= 60) {
				F23(1, C0L1S1, 766.20, 109.50, 458, 147, 100, 0, 0, 0);
			}
			if (V13 >= 0 && V13 < 16) {
				Pos = (V13 - 0) / 16;
				Pos = F6(Pos);
				NX = 64 + (Pos * 3);
				NY = -88 + (Pos * 203);
				F23(1, C0L1S0, NX, NY, 34, 151, 100, 0, 0, 0);
			}
			if (V13 >= 16 && V13 < 60) {
				Pos = (V13 - 16) / 44;
				NX = 67 + (Pos * 469);
				NY = 115;
				F23(1, C0L1S0, NX, NY, 34, 151, 100, 0, 0, 0);
			}
			if (V13 >= 60 && V13 < 62) {
				Pos = (V13 - 60) / 2;
				NX = 536 + (Pos * -5);
				NY = 115;
				F23(1, C0L1S0, NX, NY, 34, 151, 100, 0, 0, 0);
			}
			if (V13 >= 62 && V13 < 64) {
				Pos = (V13 - 62) / 2;
				NX = 531 + (Pos * 5);
				NY = 115;
				F23(1, C0L1S0, NX, NY, 34, 151, 100, 0, 0, 0);
			}
			if (V13 >= 64) {
				F23(1, C0L1S0, 536, 115, 34, 151, 100, 0, 0, 0);
			}
			break;
		}
		var IntTime = parseInt(V13);
		if (IntTime != V15) {
			V15 = IntTime;
			switch (V19) {
			case 0:
				if (IntTime == 6) {
					SoundPlay("luoxia", false);
				}
				if (IntTime == 13) {
					SoundPlay("gundong", false);
				}
				break;
			}
		}
		V14 = window.setTimeout("F22();", SpanGap);
	}
	function F31(X, Y, OX, OY, Angle) {
		X -= OX;
		Y -= OY;
		var r = Angle * (Math.PI / 180);
		var ct = Math.cos(r);
		var st = Math.sin(r);
		var x = ct * X - st * Y;
		var y = st * X + ct * Y;
		var Point = new Object();
		Point.X = x + OX;
		Point.Y = y + OY;
		return Point;
	}