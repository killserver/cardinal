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
						[if {U_level}>={D_LEVEL_USER}]<option value="1"[if {level}=={D_LEVEL_USER}] selected="selected"[/if {level}=={D_LEVEL_USER}]>{L_level[1]}</option>[/if {U_level}>={D_LEVEL_USER}]
						[if {U_level}>={D_LEVEL_MODER}]<option value="2"[if {level}=={D_LEVEL_MODER}] selected="selected"[/if {level}=={D_LEVEL_MODER}]>{L_level[2]}</option>[/if {U_level}>={D_LEVEL_MODER}]
						[if {U_level}>={D_LEVEL_ADMIN}]<option value="3"[if {level}=={D_LEVEL_ADMIN}] selected="selected"[/if {level}=={D_LEVEL_ADMIN}]>{L_level[3]}</option>[/if {U_level}>={D_LEVEL_ADMIN}]
						[if {U_level}>={D_LEVEL_CUSTOMER}]<option value="4"[if {level}=={D_LEVEL_CUSTOMER}] selected="selected"[/if {level}=={D_LEVEL_CUSTOMER}]>{L_level[4]}</option>[/if {U_level}>={D_LEVEL_CUSTOMER}]
						[if {U_level}>={D_LEVEL_CREATOR}]<option value="5"[if {level}=={D_LEVEL_CREATOR}] selected="selected"[/if {level}=={D_LEVEL_CREATOR}]>{L_level[5]}</option>[/if {U_level}>={D_LEVEL_CREATOR}]
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
										<img src="{users.avatar}" class="img-circle" alt="user-pic" width="103" />
									</a>
								</td>
								<td class="user-name">
									<a href="./?pages=Users&mod=Edit&id={users.id}" class="name">{users.username}</a> <span style="font-weight:bold;color:[foreachif {users.level}=={D_LEVEL_USER}]grey[/foreachif {users.level}=={D_LEVEL_USER}][foreachif {users.level}=={D_LEVEL_MODER}]green[/foreachif {users.level}=={D_LEVEL_MODER}][foreachif {users.level}=={D_LEVEL_ADMIN}]red[/foreachif {users.level}=={D_LEVEL_ADMIN}][foreachif {users.level}=={D_LEVEL_CUSTOMER}]red[/foreachif {users.level}=={D_LEVEL_CUSTOMER}][foreachif {users.level}=={D_LEVEL_CREATOR}]red[/foreachif {users.level}=={D_LEVEL_CREATOR}];">{L_level[{users.level}]}</span>
								</td>
								<td class="hidden-xs hidden-sm">
									<span class="email"><a href="mailto:{users.email}" target="_blank">{users.email}</a></span>
								</td>
								<td class="action-links">
									<a href="./?pages=Users&mod=Edit&id={users.id}" class="edit"> <i class="linecons-pencil"></i> {L_edit}</a>
									[foreachif {users.id}!={U_id}]<a href="./?pages=Users&mod=Delete&id={users.id}" class="delete"> <i class="linecons-trash"></i> {L_delete}</a>[/foreachif {users.id}!={U_id}]
									[foreachif {users.level}<={D_LEVEL_ADMIN}&&{users.id}!={U_id}]<a href="./?pages=Users&mod=Login&id={users.id}">{L_login_on}</a>[/foreachif {users.level}<={D_LEVEL_ADMIN}&&{users.id}!={U_id}]
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
					</div>
				</form>
			</div>
			<div class="tab-pane" id="admin">
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
							[foreach block=usersAdmin]<tr>
								<td class="user-cb">
									<input type="checkbox" class="cbr" name="members-list[]" value="{usersAdmin.id}" />
								</td>
								<td class="user-id">{usersAdmin.id}</td>
								<td class="user-image hidden-xs hidden-sm">
									<a href="./?pages=Users&mod=Edit&id={usersAdmin.id}">
										<img src="{usersAdmin.avatar}" class="img-circle" alt="user-pic" width="103" />
									</a>
								</td>
								<td class="user-name">
									<a href="./?pages=Users&mod=Edit&id={usersAdmin.id}" class="name">{usersAdmin.username}</a> <span style="font-weight:bold;color:[foreachif {usersAdmin.level}=={D_LEVEL_USER}]grey[/foreachif {usersAdmin.level}=={D_LEVEL_USER}][foreachif {usersAdmin.level}=={D_LEVEL_MODER}]green[/foreachif {usersAdmin.level}=={D_LEVEL_MODER}][foreachif {usersAdmin.level}=={D_LEVEL_ADMIN}]red[/foreachif {usersAdmin.level}=={D_LEVEL_ADMIN}][foreachif {usersAdmin.level}=={D_LEVEL_CUSTOMER}]red[/foreachif {usersAdmin.level}=={D_LEVEL_CUSTOMER}][foreachif {usersAdmin.level}=={D_LEVEL_CREATOR}]red[/foreachif {usersAdmin.level}=={D_LEVEL_CREATOR}];">{L_level[{usersAdmin.level}]}</span>
								</td>
								<td class="hidden-xs hidden-sm">
									<span class="email"><a href="mailto:{usersAdmin.email}" target="_blank">{usersAdmin.email}</a></span>
								</td>
								<td class="action-links">
									<a href="./?pages=Users&mod=Edit&id={usersAdmin.id}" class="edit"> <i class="linecons-pencil"></i> {L_edit}</a>
									[foreachif {usersAdmin.id}!={U_id}]<a href="./?pages=Users&mod=Delete&id={usersAdmin.id}" class="delete"> <i class="linecons-trash"></i> {L_delete}</a>[/foreachif {usersAdmin.id}!={U_id}]
									[foreachif {usersAdmin.level}<={D_LEVEL_ADMIN}&&{usersAdmin.id}!={U_id}]<a href="./?pages=Users&mod=Login&id={usersAdmin.id}">{L_login_on}</a>[/foreachif {usersAdmin.level}<={D_LEVEL_ADMIN}&&{usersAdmin.id}!={U_id}]
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
					</div>
				</form>
			</div>
			<div class="tab-pane" id="moder">
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
							[foreach block=usersModer]<tr>
								<td class="user-cb">
									<input type="checkbox" class="cbr" name="members-list[]" value="{usersModer.id}" />
								</td>
								<td class="user-id">{usersModer.id}</td>
								<td class="user-image hidden-xs hidden-sm">
									<a href="./?pages=Users&mod=Edit&id={usersModer.id}">
										<img src="{usersModer.avatar}" class="img-circle" alt="user-pic" width="103" />
									</a>
								</td>
								<td class="user-name">
									<a href="./?pages=Users&mod=Edit&id={usersModer.id}" class="name">{usersModer.username}</a> <span style="font-weight:bold;color:[foreachif {usersModer.level}=={D_LEVEL_USER}]grey[/foreachif {usersModer.level}=={D_LEVEL_USER}][foreachif {usersModer.level}=={D_LEVEL_MODER}]green[/foreachif {usersModer.level}=={D_LEVEL_MODER}][foreachif {usersModer.level}=={D_LEVEL_ADMIN}]red[/foreachif {usersModer.level}=={D_LEVEL_ADMIN}][foreachif {usersModer.level}=={D_LEVEL_CUSTOMER}]red[/foreachif {usersModer.level}=={D_LEVEL_CUSTOMER}][foreachif {usersModer.level}=={D_LEVEL_CREATOR}]red[/foreachif {usersModer.level}=={D_LEVEL_CREATOR}];">{L_level[{usersModer.level}]}</span>
								</td>
								<td class="hidden-xs hidden-sm">
									<span class="email"><a href="mailto:{usersModer.email}" target="_blank">{usersModer.email}</a></span>
								</td>
								<td class="action-links">
									<a href="./?pages=Users&mod=Edit&id={usersModer.id}" class="edit"> <i class="linecons-pencil"></i> {L_edit}</a>
									[foreachif {usersModer.id}!={U_id}]<a href="./?pages=Users&mod=Delete&id={usersModer.id}" class="delete"> <i class="linecons-trash"></i> {L_delete}</a>[/foreachif {usersModer.id}!={U_id}]
									[foreachif {usersModer.level}<={D_LEVEL_ADMIN}&&{usersModer.id}!={U_id}]<a href="./?pages=Users&mod=Login&id={usersModer.id}">{L_login_on}</a>[/foreachif {usersModer.level}<={D_LEVEL_ADMIN}&&{usersModer.id}!={U_id}]
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
					</div>
				</form>
			</div>
			<div class="tab-pane" id="user">
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
							[foreach block=usersUser]<tr>
								<td class="user-cb">
									<input type="checkbox" class="cbr" name="members-list[]" value="{usersUser.id}" />
								</td>
								<td class="user-id">{usersUser.id}</td>
								<td class="user-image hidden-xs hidden-sm">
									<a href="./?pages=Users&mod=Edit&id={usersUser.id}">
										<img src="{usersUser.avatar}" class="img-circle" alt="user-pic" width="103" />
									</a>
								</td>
								<td class="user-name">
									<a href="./?pages=Users&mod=Edit&id={usersUser.id}" class="name">{usersUser.username}</a> <span style="font-weight:bold;color:[foreachif {usersUser.level}=={D_LEVEL_USER}]grey[/foreachif {usersUser.level}=={D_LEVEL_USER}][foreachif {usersUser.level}=={D_LEVEL_MODER}]green[/foreachif {usersUser.level}=={D_LEVEL_MODER}][foreachif {usersUser.level}=={D_LEVEL_ADMIN}]red[/foreachif {usersUser.level}=={D_LEVEL_ADMIN}][foreachif {usersUser.level}=={D_LEVEL_CUSTOMER}]red[/foreachif {usersUser.level}=={D_LEVEL_CUSTOMER}][foreachif {usersUser.level}=={D_LEVEL_CREATOR}]red[/foreachif {usersUser.level}=={D_LEVEL_CREATOR}];">{L_level[{usersUser.level}]}</span>
								</td>
								<td class="hidden-xs hidden-sm">
									<span class="email"><a href="mailto:{usersUser.email}" target="_blank">{usersUser.email}</a></span>
								</td>
								<td class="action-links">
									<a href="./?pages=Users&mod=Edit&id={usersUser.id}" class="edit"> <i class="linecons-pencil"></i> {L_edit}</a>
									[foreachif {usersUser.id}!={U_id}]<a href="./?pages=Users&mod=Delete&id={usersUser.id}" class="delete"> <i class="linecons-trash"></i> {L_delete}</a>[/foreachif {usersUser.id}!={U_id}]
									[foreachif {usersUser.level}<={D_LEVEL_ADMIN}&&{usersUser.id}!={U_id}]<a href="./?pages=Users&mod=Login&id={usersUser.id}">{L_login_on}</a>[/foreachif {usersUser.level}<={D_LEVEL_ADMIN}&&{usersUser.id}!={U_id}]
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
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
[/if {is_edit}==0]