
			<div class="row">
				
				[if {is_new}==1]<div class="col-sm-3">
					<div class="xe-widget xe-counter xe-counter-red">
					<div class="xe-icon"><i class="linecons-params"></i></div>
					<div class="xe-label">
						<strong class="num">{new_version}</strong>
						<span>Новая версия</span>
					</div>
					</div>
				</div>[/if]
			
				<div class="col-sm-3">
					
					<div class="xe-widget xe-counter" data-count=".num" data-from="0" data-to="99.9" data-suffix="%" data-duration="2">
						<div class="xe-icon">
							<i class="linecons-cloud"></i>
						</div>
						<div class="xe-label">
							<strong class="num">0.0%</strong>
							<span>Server uptime</span>
						</div>
					</div>
					
				</div>
				
				<div class="col-sm-3">
					
					<div class="xe-widget xe-counter xe-counter-blue" data-count=".num" data-from="1" data-to="{users}" data-duration="3" data-easing="false">
						<div class="xe-icon">
							<i class="linecons-user"></i>
						</div>
						<div class="xe-label">
							<strong class="num">{users}</strong>
							<span>Users Total</span>
						</div>
					</div>
				
				</div>
				
				[if {is_new}==1]
				<div class="col-sm-12">
					<div class="panel-body">
						<div class="scrollable" data-max-height="400">
							{changelog}
						</div>
					</div>
				</div>
				[/if]
			</div>