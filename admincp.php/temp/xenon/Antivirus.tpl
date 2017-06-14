<style>
@keyframes loadAntivirus1 {
	0% {
		transform: rotate(0deg);
	}
	16% {
		transform: rotate(90deg);
	}
	32% {
		transform: rotate(180deg);
	}
	48% {
		transform: rotate(270deg);
	}
	64% {
		transform: rotate(360deg);
	}
	80% {
		transform: rotate(450deg);
	}
	96% {
		transform: rotate(540deg);
	}
	100% {
		transform: rotate(540deg);
	}
}
@keyframes loadAntivirus2 {
	0%, 100% {
		transform: scale(0.8);
		border-radius: 0%;
	}
	50% {
		border-radius: 30%;
	}
	25%, 75% {
		transform: scale(1);
		border-radius: 0%;
	}
}
.sidebar-menu.none {
	display: none;
}
.page-container .main-content {
	position: initial;
}
.antivirusBG {
	position: fixed;
	top: 0px;
	left: 0px;
	width: 100%;
	height: 100%;
	z-index: 100000;
	background: #fff;
}
.antivirus {
	position: absolute;
	top: 35vh;
	left: 40vw;
}
.antivirus > .loaderAntivirus {
	animation: loadAntivirus1 10s infinite;
	width: 11vw;
	height: 22vh;
	display: table;
	margin: 0px auto;
}
.antivirus > .loaderAntivirus > .load {
	animation: loadAntivirus2 3s infinite;
	background: red;
	width: 50%;
	height: 50%;
	float: left;
}
.antivirus > .loaderAntivirus > .load.green {
	background: green;
}
.antivirus > .loaderAntivirus > .load.blue {
	background: blue;
}
.antivirus > .loaderAntivirus > .load.purple {
	background: purple;
}
.antivirus > .status {
	margin: 10vh 0px 0px;
	font-size: 18px;
	text-align: center;
	width: 18vw;
}
</style>
<div class="antivirusBG">
	<div class="antivirus">
		<div class="loaderAntivirus">
			<div class="load"></div>
			<div class="load green"></div>
			<div class="load blue"></div>
			<div class="load purple"></div>
		</div>
		<div class="status"></div>
	</div>
</div>
<div class="antivirusResp"></div>
<script>
function rand(min,max) {if(max){return Math.floor(Math.random()*(max-min+1))+min;}else{return Math.floor(Math.random()*(min+1));}}
jQuery(".sidebar-menu").addClass("none");
var randTime = 0;
function ViewAntivirus(data) {
	var html = "<div class=\"row\"><div class=\"col-sm-12\"><div class=\"panel panel-default\"><table class=\"table responsive\" width=\"100%\">";
	html += "<thead><tr><th>{L_"Путь к файлу"}</th><th>{L_"Действие"}</th></tr></thead><tbody>";
	for(var i=0;i<data.length;i++) {
		var actionWith = "";
		switch(data[i].alert) {
			case "alert":
				actionWith = "{L_"Удалите данный файл - он представляет угрозу для безопасности"}";
			break;
			case "warning":
				actionWith = "{L_"Возможно не представляет угрозы, но всё-же - обратитесь в службу тех.поддержки"}";
			break;
		}
		html += "<tr><td>"+data[i].path+"</td><td>"+actionWith+"</td></tr>";
	}
	html += "</tbody></table></div></div></div>";
	jQuery(".antivirusResp").html(html);
	jQuery(".antivirusBG").animate({"opacity": "0"}, function() {
		jQuery(".sidebar-menu").removeClass("none");
		jQuery(".antivirusBG").css("display", "none");
		jQuery(".page-container .main-content").css("position", "relative");
	});
}
setTimeout(function() {
	jQuery(".antivirus > .status").html("{L_"Инициализация антивирусной защиты"}");
	randTime = rand(1, 3)*1000;
	setTimeout(function() {
		jQuery.post("./?pages=Antivirus", "page=Init").done(function(data) {
			jQuery(".antivirus > .status").html("{L_"Запуск антивирусной защиты"}<br><b>{L_"завершено"}</b>");
			randTime = 2000;
			setTimeout(function() {
				jQuery(".antivirus > .status").html("{L_"Инициализация антивирусного сканера"}");
				randTime = rand(5, 7)*1000;
				setTimeout(function() {
					jQuery(".antivirus > .status").html("{L_"Запуск антивирусного сканера"}");
					jQuery.post("./?pages=Antivirus", "page=Scan").done(function(data) {
						jQuery(".antivirus > .status").html("{L_"Запуск антивирусного сканера"}<br><b>{L_"завершено"}</b>");
						randTime = rand(2, 3)*1000;
						setTimeout("ViewAntivirus("+data+")", randTime);
					}, "json");
				}, randTime);
			}, randTime);
		});
	}, randTime);
}, 500);
</script>