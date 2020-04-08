<div id="root"></div>
<script type="text/template" class="template">
	<div class="panel panel-default">
		<div class="panel-heading">Сортировка "{type}"</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-12">
					<ul id="nestable-list-{category}" class="uk-nestable" data-uk-nestable="{maxDepth:1}">
						{items}
					</ul>
				</div>
			</div>
		</div>
	</div>
</script>
<script type="text/template" class="template_item">
	<li data-id="{id}" data-visible="{visible}">
		<div class="uk-nestable-item">
			<div class="uk-nestable-handle"></div>
			<div data-nestable-action="toggle"></div>
			<div class="list-label">{title}</div>
		</div>
	</li>
</script>
<script type="text/template" class="json">{json}</script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		var json = JSON.parse($(".json").html());
		var tpl = "";
		var catI = 0;
		console.warn(json)
		Object.keys(json).forEach(function(key) {
			var tmp = $(".template").html();
			var subTpl = "";
			for(var i=0;i<json[key].length;i++) {
				var tmpItem = $(".template_item").html();
				tmpItem = tmpItem.replace(new RegExp("{id}", "g"), json[key][i].id);
				tmpItem = tmpItem.replace(new RegExp("{title}", "g"), json[key][i].title);
				tmpItem = tmpItem.replace(new RegExp("{visible}", "g"), (typeof(json[key][i].visible)==="undefined" || json[key][i].visible=="Да" ? "true" : "false"));
				subTpl += tmpItem;
			}
			tmp = tmp.replace(new RegExp("{type}", "g"), key);
			tmp = tmp.replace(new RegExp("{category}", "g"), catI);
			tmp = tmp.replace(new RegExp("{items}", "g"), subTpl);
			tpl += tmp;
			catI++;
		});
		$("#root").html(tpl);
		$("[id*='nestable-list']").on('nestable-stop', function(ev) {
			var serialized = $(this).data('nestable').serialize()
			console.log(serialized);
			$.post(window.location.href, {"serialized": serialized}, function() {})
		});
	});
</script>
<style>
[data-visible='false'] .uk-nestable-item {
	background-color: #ccc;
}
</style>