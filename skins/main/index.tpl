[SET {fileExists}={{FN_'file_exists','{D_PATH_MEDIA}firstUser.lock'}}]
<div class="centred {fileExists}">
	<div>
		<img src="{THEME}/../core/cardinal.svg">
		<p>Поздравляем с успешной установкой <b>Cardinal Engine</b>!<br>Приятного использования!</p>
		[if {fileExists}==false]
		<a href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/" target="_blank">Войти в админ панель</a>
		<p>Доступы к админ-панели:</p>
		<div><label for="">Логин:</label><input type="text" readonly="readonly" value="{FN_'User::getUserData','root;username'}" onclick="this.select()"></div>
		<div><label for="">Пароль:</label><input type="text" readonly="readonly" value="{FN_'User::getUserData','root;light'}" onclick="this.select()"></div>
		<span style="display:none;">{FN_'file_put_contents','{D_PATH_MEDIA}firstUser.lock;null'}</span>
		[/if {fileExists}==false]
	</div>
</div>
<style>
	body {
		max-height: 100%;
		overflow: hidden;
	}
	.centred {
		display: inline-flex;
		align-items: center;
		height: 100vh;
		width: 100%;
		color: #fff;
		text-shadow: 0px 1px 6px #000;
		font-size: 1.1em;
	}
	.centred:before {
		content: '';
		background: url('https://images5.alphacoders.com/700/thumb-1920-700049.jpg');
		position: absolute;
		top: 0em;
		left: 0em;
		z-index: -1;
		width: 100%;
		height: 100%;
		background-size: cover;
		background-position: center center;
		-webkikt-filter: url('#myblurfilter') hue-rotate(40deg); 
		filter: url('#myblurfilter') hue-rotate(40deg);
		opacity: 0.75;
	}
	.centred:after {
		content: '';
		position: absolute;
		top: 0px;
		left: 0px;
		width: 100%;
		height: 100%;
		z-index: -2;
		background: #000
	}
	.centred > div {
		margin: 0px auto;
		font-family: 'Roboto',sans-serif;
		filter: drop-shadow(0px 1px 1px #222);
		font-weight: 500;
		letter-spacing: 0.03em;
		color: #eee;
		margin-top: 4.25em;
		text-align: center;
		line-height: 1.6em
	}
	.centred.false > div {
		margin-top: 17.25em;
	}
	.centred > div > div {
		margin-bottom: 1em;
	}
	.centred > div > img {
		height: 80px;
		margin: 0px auto 2em;
		display: table
	}
	.centred > div > p {
		margin: 0px 0px 0.75em;
	}
	.centred > div > a {
		border-radius: 0.3em;
		padding: 1em;
		display: inline-block;
		margin: 1em auto;
		color: #000;
		background: #fff;
		text-shadow: none;
		letter-spacing: 0.01em;
		transition: all 300ms ease-in-out;
		text-decoration: none;
	}
	.centred > div > a:hover {
		background: #0054b5;
		color: #fff;
	}
	.centred label {
		text-align: right;
		margin-right: 1em;
		min-width: 75px;
		display: inline-block;
	}
	.centred input {
		border: 1px solid #fff;
		border-radius: 0.3em;
		padding: 0.4em;
		color: #fff;
		width: 220px;
		background: transparent;
	}
</style>
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="0" style="display:none;">
	<filter id="myblurfilter" width="110%" height="110%">
		<feColorMatrix in="SourceGraphic" type="saturate" values="1.5" />
		<feGaussianBlur stdDeviation="5" result="blur" />
	</filter>
</svg>