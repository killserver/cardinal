[if {is_edit}==1||{is_edit}==2]
<div class="panel panel-default">
	<div class="panel-heading">[if {is_edit}==1]Редактирование[else {is_edit}==1]Добавление[/if {is_edit}==1] пользователя</div>
	<div class="panel-body">
		<form role="form" class="form-horizontal" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="field-1">{L_name}</label>
				<div class="col-sm-10">
					<input type="text" name="name" class="form-control" id="field-1" placeholder="Введите имя пользователя" value="{name}" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="field-1">Пароль</label>
				<div class="col-sm-10">
					<input type="password" name="password" class="form-control" id="field-1" placeholder="Введите пароль пользователя" value="{light}" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="field-4">Права доступа</label>
				<div class="col-sm-10">
					<select name="level">
						<option value="1"[if {level}=={D_LEVEL_USER}] selected="selected"[/if]>Пользователь</option>
						<option value="2"[if {level}=={D_LEVEL_MODER}] selected="selected"[/if]>Администратор</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="field-1">Электронная почта</label>
				<div class="col-sm-10">
					<input type="email" name="email" class="form-control" id="field-1" placeholder="Введите электронную почту пользователя" value="{email}" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="field-4">Включенно?</label>
				<div class="col-sm-10">
					<input type="checkbox" name="activ" id="activ" [if {activ}=="yes"] checked="checked"[/if {activ}=="yes"] class="iswitch iswitch-pink" />
				</div>
			</div>
			<div class="form-group-separator"></div>
			<button type="submit" class="btn btn-purple btn-icon">
				<i class="fa-check"></i>
				<span>{L_save}</span>
			</button>
		</form>
	</div>
</div>
[/if {is_edit}==1||{is_edit}==2]
[if {is_edit}==0]
<div class="panel panel-default">
	<div class="panel-heading">Пользователи<center><a href="./?pages=Users&mod=Add" class="btn btn-info btn-icon"><i class="fa-plus"></i><span>Добавить</span></a></center></div>
	<div class="panel-body">
		[foreach block=users]<div class="member-entry">
			<a href="./?pages=Users&mod=Edit&id={users.id}" class="member-img"><img src="{users.avatar}" class="img-rounded" /><i class="entypo-forward"></i></a>
			<div class="member-details">
				<h4><a href="./?pages=Users&mod=Edit&id={users.id}">{users.username}</a></h4>
				<div class="row info-list">
					<div class="col-sm-4"><i class="entypo-briefcase"></i><font color="[foreachif {users.level}=={D_LEVEL_MODER}]red[/foreachif]">{L_level[{users.level}]}</font></div>
					<div class="col-sm-4"><a href="mailto:{users.email}" target="_blank"><i class="entypo-mail"></i>{users.email}</a></div>
					<div class="col-sm-4">
						<a href="./?pages=Users&mod=Edit&id={users.id}" class="btn btn-success btn-icon"><i class="fa-pencil"></i><span>Редактировать</span></a><br />
						<a href="./?pages=Users&mod=Delete&id={users.id}" class="btn btn-red btn-icon"><i class="fa-remove"></i><span>Удалить</span></a>
					</div>
				</div>
			</div>
		</div>[/foreach]
	</div>
</div>
<link rel="stylesheet" href="http://demo.neontheme.com/assets/css/neon-theme.css" />
[/if {is_edit}==0]