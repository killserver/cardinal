<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<style>
	body {
		margin: 0px;
		padding: 0px;
		font-family: 'Open Sans', sans-serif;
		font-weight: 300;
		background-image: url(http://bootstraptema.ru/coming-soon/2016/afteraworkcp/images/picjumbo.com_IMG_9076.jpg);
		display: flex;
		align-items: center;
		justify-content: center;
		height: 100vh;
		flex-direction: column;
	}
	body:after {
		content: '';
		position: fixed;
		top: 0px;
		left: 0px;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.5);
		display: block;
		z-index: 2;
	}
	body > .head {
		font-size: 56px;
		font-weight: 300;
		color: #fff;
		margin: 30px 0 30px 0;
		filter: drop-shadow(0 1px 2px rgba(0, 0, 0, .6));
		position: relative;
		z-index: 3;
	}
	body > .logo {
	    position: relative;
	    z-index: 3;
	}
	body > .logo img {
		width: 150px;
		height: 50px;
		filter: drop-shadow(0 1px 2px rgba(0, 0, 0, .6));
	}
	.text {
		margin-bottom: 50px;
		position: relative;
		z-index: 3;
		color: #fff;
		font-size: 17px;
		text-align: center;
		line-height: 1.6;
		width: 50%;
	}
	.btn {
		display: inline-block;
		margin-bottom: 50px;
		font-weight: 400;
		text-align: center;
		vertical-align: middle;
		cursor: pointer;
		background-image: none;
		border: 1px solid transparent;
		white-space: nowrap;
		line-height: 1.428571429;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		-o-user-select: none;
		user-select: none;
		text-transform: uppercase;
		padding: 8px 12px;
		min-width: 120px;
		-webkit-border-radius: 2px;
		-moz-border-radius: 2px;
		-ms-border-radius: 2px;
		-o-border-radius: 2px;
		border-radius: 2px;
		transition: all 0.4s;
		font-size: 12px;
		letter-spacing: 0.4px;
		color: #fff;
		position: relative;
		z-index: 3;
		text-decoration: none;
		border: 1px solid;
	}
	.btn:hover {
		color: #68C39F;
		color: rgba(0, 0, 0, .6);
		background: #fff;
		border-color: #fff;
		filter: drop-shadow(0 1px 2px rgba(0, 0, 0, .6));
	}
	</style>
</head>
<body>
	<div class="logo"><img src="../../admincp.php/assets/xenon/images/logo.svg"></div>
	<div class="head">{L_"Обновление"}</div>
	<div class="text">{L_"Производится обновление ядра сайта. Пожалуйста, попробуйте обновить страницу немного позже"}</div>
	<a href="#" class="btn" onclick="window.location.reload();return false;">{L_"Обновить страницу"}</a>
</body>
</html>