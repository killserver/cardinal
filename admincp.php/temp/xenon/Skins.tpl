<div style="display:inline-block;width:50%;">{L_skin_site}:</div><div style="display:inline-block;"><select name="skins">
[foreach block=skin_list]<option value="{skin_list.skin}"[foreachif {skin_list.selected}==1] selected="selected"[/foreachif {skin_list.selected}==1]>{skin_list.skin}</option>
[/foreach]</select></div>
<div style="display:inline-block;width:50%;">{L_skin_mobile}:</div><div style="display:inline-block;"><select name="skins_mobile">
[foreach block=skin_list_mobile]<option value="{skin_list_mobile.skin}"[foreachif {skin_list_mobile.selected}==1] selected="selected"[/foreachif {skin_list_mobile.selected}==1]>{skin_list_mobile.skin}</option>
[/foreach]</select></div>
<div style="display:inline-block;width:50%;">{L_skin_admin}:</div><div style="display:inline-block;"><select name="skins_admin">
[foreach block=skin_list_admin]<option value="{skin_list_admin.skin}"[foreachif {skin_list_admin.selected}==1] selected="selected"[/foreachif {skin_list_admin.selected}==1]>{skin_list_admin.skin}</option>
[/foreach]</select></div>