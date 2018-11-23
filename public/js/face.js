var face = {
	facePath:[
	    {faceName:"微笑",facePath:"0_1.gif"},
		{faceName:"撇嘴",facePath:"1_2.gif"},
		{faceName:"色",facePath:"2_3.gif"},
		{faceName:"发呆",facePath:"3_4.gif"},
		{faceName:"得意",facePath:"4_5.gif"},
		{faceName:"流泪",facePath:"5_6.gif"},
		{faceName:"害羞",facePath:"6_7.gif"},
		{faceName:"闭嘴",facePath:"7_8.gif"},
		{faceName:"大哭",facePath:"9_9.gif"},
		{faceName:"尴尬",facePath:"10_10.gif"},
		{faceName:"发怒",facePath:"11_11.gif"},
		{faceName:"调皮",facePath:"12_12.gif"},
		{faceName:"龇牙",facePath:"13_13.gif"},
		{faceName:"惊讶",facePath:"14_14.gif"},
		{faceName:"难过",facePath:"15_15.gif"},
		{faceName:"酷",facePath:"16_16.gif"},
		{faceName:"冷汗",facePath:"17_17.gif"},
		{faceName:"抓狂",facePath:"18_18.gif"},
		{faceName:"吐",facePath:"19_19.gif"},
		{faceName:"偷笑",facePath:"20_20.gif"},
	    {faceName:"可爱",facePath:"21_21.gif"},
		{faceName:"白眼",facePath:"22_22.gif"},
		{faceName:"傲慢",facePath:"23_23.gif"},
		{faceName:"饥饿",facePath:"24_24.gif"},
		{faceName:"困",facePath:"25_25.gif"},
		{faceName:"惊恐",facePath:"26_26.gif"},
		{faceName:"流汗",facePath:"27_27.gif"},
		{faceName:"憨笑",facePath:"28_28.gif"},
		{faceName:"大兵",facePath:"29_29.gif"},
		{faceName:"奋斗",facePath:"30_30.gif"},
		{faceName:"咒骂",facePath:"31_31.gif"},
		{faceName:"疑问",facePath:"32_32.gif"},
		{faceName:"嘘",facePath:"33_33.gif"},
		{faceName:"晕",facePath:"34_34.gif"},
		{faceName:"折磨",facePath:"35_35.gif"},
		{faceName:"衰",facePath:"36_36.gif"},
		{faceName:"骷髅",facePath:"37_37.gif"},
		{faceName:"敲打",facePath:"38_38.gif"},
		{faceName:"再见",facePath:"39_39.gif"},
		{faceName:"擦汗",facePath:"40_40.gif"},
		
		{faceName:"抠鼻",facePath:"41_41.gif"},
		{faceName:"鼓掌",facePath:"42_42.gif"},
		{faceName:"糗大了",facePath:"43_43.gif"},
		{faceName:"坏笑",facePath:"44_44.gif"},
		{faceName:"左哼哼",facePath:"45_45.gif"},
		{faceName:"右哼哼",facePath:"46_46.gif"},
		{faceName:"哈欠",facePath:"47_47.gif"},
		{faceName:"鄙视",facePath:"48_48.gif"},
		{faceName:"委屈",facePath:"49_49.gif"},
		{faceName:"快哭了",facePath:"50_50.gif"},
		{faceName:"阴险",facePath:"51_51.gif"},
		{faceName:"亲亲",facePath:"52_52.gif"},
		{faceName:"吓",facePath:"53_53.gif"},
		{faceName:"可怜",facePath:"54_54.gif"},
		{faceName:"菜刀",facePath:"55_55.gif"},
		{faceName:"西瓜",facePath:"56_56.gif"},
		{faceName:"啤酒",facePath:"57_57.gif"},
		{faceName:"篮球",facePath:"58_58.gif"},
		{faceName:"乒乓",facePath:"59_59.gif"},
		{faceName:"拥抱",facePath:"78_78.gif"},
		{faceName:"握手",facePath:"81_81.gif"}
	],
	init : function (){
		var isShowImg=false;
		$(".emotion_btn").click(function(){
			if(isShowImg==false){
				isShowImg=true;
			    if($(".faceDiv").children().length==0){
					for(var i=0;i<face.facePath.length;i++){
						$(".faceDiv").append("<img title=\""+face.facePath[i].faceName+"\" src=\"./images/face/"+face.facePath[i].facePath+"\" />");
					}
					$(".faceDiv>img").click(function(){
						isShowImg=false;
						face.insertAtCursor($("#chattext")[0],"["+$(this).attr("title")+"]");
					});
				}
				$(".faceDiv").css("display","block");
			}else{
				isShowImg=false;
				$(".faceDiv").css("display","none");
			}
		});
	},
	insertAtCursor:function(myField, myValue) {
		if (document.selection) {
			myField.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
			sel.select();
		} else if (myField.selectionStart || myField.selectionStart == "0") {
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			var restoreTop = myField.scrollTop;
			myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
			if (restoreTop > 0) {
				myField.scrollTop = restoreTop;
			}
			myField.focus();
			myField.selectionStart = startPos + myValue.length;
			myField.selectionEnd = startPos + myValue.length;
		} else {
			myField.value += myValue;
			myField.focus();
		}
		$(".faceDiv").css("display","none");
	}
}