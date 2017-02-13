			<div class="panel panel-default">
				<div class="panel-heading">{L_"Сортировка"}</div>
				<div class="panel-body">
					
					<div class="row">
						<div class="col-sm-12">
					
							<script type="text/javascript">
								jQuery(document).ready(function($)
								{
									$("#nestable-list-1").on('nestable-stop', function(ev)
									{
										var serialized = $(this).data('nestable').serialize(),
											str = '';
										str = iterateList(serialized, 0);
									});
								});
								
								function iterateList(items, depth)
								{
									var str = '';
									
									if( ! depth)
										depth = 0;
									
									console.log(items);
									
									var saved = [];
									jQuery.each(items, function(i, obj)
									{
										saved[i] = obj.itemId;
										str += '[ID: ' + obj.itemId + ']\t' + repeat('—', depth+1) + ' ' + obj.item;
										str += '\n';
										
										if(obj.children)
										{
											str += iterateList(obj.children, depth+1);
										}
									});
									jQuery.post("./?pages=Archer&type={ShowPage}&pageType=Sort&save", saved.join("%26"), function(data) {
										alert(data);
									});
									
									return str;
								}
								
								function repeat(s, n)
								{
									var a = [];
									while(a.length < n)
									{
										a.push(s);
									}
									return a.join('');
								}
								
							</script>
							<ul id="nestable-list-1" class="uk-nestable" data-uk-nestable="{maxDepth:1}">
								[foreach block={ShowSort}]<li data-item="{{ShowSort}.{ShowID}}" data-item-id="{{ShowSort}.{ShowID}}">
									<div class="uk-nestable-item">
									    <div class="uk-nestable-handle"></div>
									    <div data-nestable-action="toggle"></div>
									    <div class="list-label">{ShowName}</div>
									</div>
								</li>[/foreach]
							</ul>
						
						</div>
					</div>
					
				</div>
			</div>
			<!-- Imported styles on this page -->
			<link rel="stylesheet" href="http://online-killer.pp.ua/admincp.php/html/assets/js/uikit/uikit.css">
			<!-- Imported scripts on this page -->
			<script src="http://online-killer.pp.ua/admincp.php/html/assets/js/uikit/js/uikit.min.js"></script>
			<script src="http://online-killer.pp.ua/admincp.php/html/assets/js/uikit/js/addons/nestable.min.js"></script>