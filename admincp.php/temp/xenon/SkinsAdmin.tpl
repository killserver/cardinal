<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					[foreach block=skins]<a href="#" class="btn col-xs-12 col-md-6 col-lg-4[foreachif {skins.orName}!={C_skins[skins]}] install[/foreachif {skins.orName}!={C_skins[skins]}]" data-name="{skins.orName}">
						<div class="image">
							<div class="set">Установить</div>
							[foreachif {skins.IS_Image}==true]<img src="{skins.Image}" class="img">[/foreachif {skins.IS_Image}==true]
							[foreachif {skins.IS_Image}==false]<div class="img" style="background-image:url({skins.Image});"></div>[/foreachif {skins.IS_Image}==false]
						</div>
						[foreachif {skins.orName}=={C_skins[skins]}]<div class="title bg-success"><b>{L_"Активно"}:&nbsp;</b>{skins.Name}</div>[/foreachif {skins.orName}=={C_skins[skins]}]
						[foreachif {skins.orName}!={C_skins[skins]}]<div class="title bg-primary">{skins.Name}</div>[/foreachif {skins.orName}!={C_skins[skins]}]
					</a>[/foreach]
					<!--a href="#" class="col-xs-12 col-md-6 col-lg-3 add">
						<div class="image">
							<div class="img" style="background-image:url(data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAQAAABuBnYAAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QA/4ePzL8AAAAHdElNRQfiBRUMCyt+LeEbAAAAI0lEQVQI12O895+BgYGBgUGREUIzMaABwgKMUCMY7v8n2wwAv+QE6yFMzH8AAAAASUVORK5CYII=);"></div>
						</div>
					</a-->
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
	.panel .panel-body { padding: 0px }
	.panel div.img { height: 100%; width: 100%; position: absolute; top: 0px; left: 0px; }
	.panel div.image { height: 20rem; position: relative; overflow: hidden; }
	.panel img.img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
	a.add .image { height: 23.8rem; position: relative; }
	a.add .img { transition: all 300ms ease-in-out; }
	a.add:hover .img { background-color: #d0d0d0; }
	a.add .image:after { content: "\f067"; font-family: FontAwesome; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 5rem; line-height: 0px; color: #2c2e2f; }
	a.add:hover .image:after { color: #cc3f44; }
	.panel div.image > .set { position: absolute; top: -100%; left: 0px; width: 100%; transition: all 300ms ease-in-out; background: #40bbea; padding: 10px 15px; color: #fff; font-size: 1.25rem; z-index: 5; }
	.panel a.install:hover div.image > .set { top: 0px; }
</style>
<script type="text/javascript">
	var active = '<b>{L_"Активно"}:&nbsp;</b>';
	jQuery(document).ready(function() {
		jQuery("body").on("click", ".panel a.install", function() {
			var th = this;
			jQuery.post('./?pages=Skins&set='+jQuery(this).attr("data-name"), function(data) {
				jQuery(".panel .bg-success").find("b").remove();
				jQuery(".panel .bg-success").removeClass('bg-success').addClass('bg-primary');
				jQuery(".panel a:not(.install)").addClass('install');
				jQuery(th).removeClass('install');
				jQuery(th).find(".title").addClass('bg-success').prepend(active);
			});
			return false;
		});
	});
</script>