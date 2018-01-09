<style type="text/css">
	#error {
		background: red;
	}
	input[type='submit'] {
		width: 200px;
		margin-top: 20px;
	}
</style>
<table width="100%"><form action="/?reg" method="post" onsubmit="send_reg(); return false">
	<tr><td width="30%">{L_username}</td><td><input type="text" size="50" name="username" id="username"></td></tr>
	<tr><td width="30%">{L_pass}</td><td><input type="password" size="50" name="pass" id="pass"></td></tr>
	<tr><td width="30%">{L_repass}</td><td><input type="password" size="50" name="repass" id="repass">&nbsp;<div id="view_pass"></div></td></tr>
	<tr><td width="30%">{L_email}</td><td><input type="text" size="50" name="email" id="email"></td></tr>
	<tr><td colspan="2" align="center"><input type="submit" name="submit" onclick="send_reg(); return false"></td></tr>
</form></table>
<div id="request_reg"></div>