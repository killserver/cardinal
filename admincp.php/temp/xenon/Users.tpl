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
			[if {U_username}!={name}]<div class="form-group">
				<label class="col-sm-2 control-label" for="field-4">Права доступа</label>
				<div class="col-sm-10">
					<select name="level">
						[if {level}>={D_LEVEL_USER}]<option value="1"[if {level}=={D_LEVEL_USER}] selected="selected"[/if {level}=={D_LEVEL_USER}]>Пользователь</option>[/if {level}>={D_LEVEL_USER}]
						[if {level}>={D_LEVEL_MODER}]<option value="2"[if {level}=={D_LEVEL_MODER}] selected="selected"[/if {level}=={D_LEVEL_MODER}]>Модератор</option>[/if {level}>={D_LEVEL_MODER}]
						[if {level}>={D_LEVEL_ADMIN}]<option value="3"[if {level}=={D_LEVEL_ADMIN}] selected="selected"[/if {level}=={D_LEVEL_ADMIN}]>Администратор</option>[/if {level}>={D_LEVEL_ADMIN}]
					</select>
				</div>
			</div>[/if {U_username}!={name}]
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
<center><a href="./?pages=Users&mod=Add" class="btn btn-info btn-icon"><i class="fa-plus"></i><span>Добавить</span></a></center>
<form method="get" action="./">
	<input type="hidden" name="pages" value="Users" />
	<input type="hidden" name="search" />
	<div class="row">
		<div class="col-md-3">
			{L_search_by_ip}
		</div>
		<div class="col-md-9">
			<div class="input-group">
				<div class="input-group-addon">
					<i class="fa fa-laptop"></i>
				</div>
				<input class="form-control" name="ip" data-inputmask="'alias': 'ip'" data-mask="" type="text" value="{search_ip}" />
			</div>
		</div>
	</div>
	<div class="row">
		<input class="" type="submit" />
	</div>
</form>
<div class="row">
	<div class="col-md-12">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#all" data-toggle="tab">{L_member_list}</a>
			</li>
			<li>
				<a href="#admin" data-toggle="tab">{L_admins}</a>
			</li>
			<li>
				<a href="#moder" data-toggle="tab">{L_moders}</a>
			</li>
			<li>
				<a href="#user" data-toggle="tab">{L_users}</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="all">
				<form method="post" action="./?pages=Users&mod=Mass">
					<table class="table table-hover members-table middle-align">
						<thead>
							<tr>
								<th></th>
								<th>ID</th>
								<th class="hidden-xs hidden-sm">{L_images}</th>
								<th>{L_username} & {L_group}</th>
								<th class="hidden-xs hidden-sm">{L_email}</th>
								<th>{L_settings}</th>
							</tr>
						</thead>
						<tbody>
							[foreach block=users]<tr>
								<td class="user-cb">
									<input type="checkbox" class="cbr" name="members-list[]" value="{users.id}" />
								</td>
								<td class="user-id">{users.id}</td>
								<td class="user-image hidden-xs hidden-sm">
									<a href="./?pages=Users&mod=Edit&id={users.id}">
										<img src="{users.avatar}" class="img-circle" alt="user-pic" />
									</a>
								</td>
								<td class="user-name">
									<a href="./?pages=Users&mod=Edit&id={users.id}" class="name">{users.username}</a> <span style="font-weight:bold;color:[foreachif {users.level}=={D_LEVEL_USER}]grey[/foreachif {users.level}=={D_LEVEL_USER}][foreachif {users.level}=={D_LEVEL_MODER}]green[/foreachif {users.level}=={D_LEVEL_MODER}][foreachif {users.level}=={D_LEVEL_ADMIN}]red[/foreachif {users.level}=={D_LEVEL_ADMIN}];">{L_level[{users.level}]}</span>
								</td>
								<td class="hidden-xs hidden-sm">
									<span class="email"><a href="mailto:{users.email}" target="_blank">{users.email}</a></span>
								</td>
								<td class="action-links">
									<a href="./?pages=Users&mod=Edit&id={users.id}" class="edit"> <i class="linecons-pencil"></i> {L_edit}</a>
									[foreachif {users.id}!={U_id}]<a href="./?pages=Users&mod=Delete&id={users.id}" class="delete"> <i class="linecons-trash"></i> {L_delete}</a>[/foreachif {users.id}!={U_id}]
									[foreachif {users.level}=={D_LEVEL_ADMIN}]<a href="./?pages=Users&mod=Login&id={users.id}">{L_login_on}</a>[/foreachif {users.level}=={D_LEVEL_ADMIN}]
								</td>
							</tr>[/foreach]
						</tbody>
					</table>
					<div class="row">
					<div class="col-sm-6">
						<div class="members-table-actions">
							<div class="selected-actions">
								<select name="action">
									<option value="">---</option>
									<option value="edit">{L_edit}</option>
									<option value="delete">{L_delete}</option>
								</select>
								<input type="submit" name="submit" value="{L_submit}" />
							</div>
						</div>
					</div>
					<div class="col-sm-6 text-right text-center-sm">
						<ul class="pagination pagination-sm no-margin">
							<li>
								<a href="#">
									<i class="fa-angle-left"></i>
								</a>
							</li>
							<li class="active">
								<a href="#">1</a>
							</li>
							<li>
								<a href="#">
									<i class="fa-angle-right"></i>
								</a>
							</li>
						</ul>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
[/if {is_edit}==0]