<span id="servercpu" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12"[if {cpuUseVisible}=="0"] style="display:none;"[/if {cpuUseVisible}=="0"]>
  <div class="chart-item-bg">
    <div class="chart-label">
      <div id="cpuUseHtml" class="h1 text-secondary text-bold" data-count="this" data-from="0.00" data-to="{cpuUse}" data-suffix="%" data-duration="1.5">0.00%</div>
      <span class="text-small text-muted text-upper">CPU Usage</span>
    </div>
    <div id="other-stats" style="min-height: 183px">
      <div id="cpu-usage-gauge" style="width: 170px; height: 140px; position: absolute; right: 20px; top: 20px"></div>
    </div>
  </div>
</span>
<span id="servermem" class="col-md-[if {C_FullMenu}==1]4[/if {C_FullMenu}==1][if {C_FullMenu}!=1]3[/if {C_FullMenu}!=1] col-sm-12"[if {memUseVisible}=="0"] style="display:none;"[/if {memUseVisible}=="0"]>
  <div class="chart-item-bg">
    <div class="chart-label">
      <div id="memUseHtml" class="h1 text-secondary text-bold" data-count="this" data-from="0.00" data-to="{memUse}" data-suffix="%" data-duration="1.5">0.00%</div>
      <span class="text-small text-muted text-upper">Memory Usage</span>
    </div>
    <div id="other-stats" style="min-height: 183px">
      <div id="mem-usage-gauge" style="width: 170px; height: 140px; position: absolute; right: 20px; top: 20px"></div>
    </div>
  </div>
</span>
<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/devexpress-web-14.1x/js/globalize.min.js" id="script-resource-8"></script>
<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/devexpress-web-14.1x/js/dx.chartjs.js" id="script-resource-9"></script>
<script[if {cpuUseVisible}=="0"||{memUseVisible}=="0"] type="text/notimplemented"[/if {cpuUseVisible}=="0"||{memUseVisible}=="0"]>
var cpuUse = $("#cpu-usage-gauge").dxCircularGauge({
  scale: {
    startValue: 0,
    endValue: 100,
    majorTick: {
      tickInterval: 25
    }
  },
  rangeContainer: {
    palette: 'pastel',
    width: 3,
    ranges: [
      { startValue: 0, endValue: 25, color: "#68b828" },
      { startValue: 25, endValue: 50, color: "#68b828" },
      { startValue: 50, endValue: 75, color: "#68b828" },
      { startValue: 75, endValue: 100, color: "#d5080f" },
    ],
  },
  value: {cpuUse},
  valueIndicator: {
    offset: 10,
    color: '#68b828',
    type: 'rectangleNeedle',
    spindleSize: 12
  }
}).dxCircularGauge("instance");

var memUse = $("#mem-usage-gauge").dxCircularGauge({
  scale: {
    startValue: 0,
    endValue: 100,
    majorTick: {
      tickInterval: 25
    }
  },
  rangeContainer: {
    palette: 'pastel',
    width: 3,
    ranges: [
      { startValue: 0, endValue: 25, color: "#40bbea" },
      { startValue: 25, endValue: 50, color: "#40bbea" },
      { startValue: 50, endValue: 75, color: "#40bbea" },
      { startValue: 75, endValue: 100, color: "#40bbea" },
    ],
  },
  value: {memUse},
  valueIndicator: {
    offset: 10,
    color: '#2c2e2f',
    type: 'rectangleNeedle',
    spindleSize: 12
  }
}).dxCircularGauge("instance");
var opts = {
  useEasing: true,
  useGrouping: true,
  separator: ',',
  decimal: '.',
  prefix: '',
  suffix: '%',
}
setInterval(function() {
  jQuery.post("./?getServerLoad", function(data) {
    var to = data[0];
    var from = parseInt(jQuery("#cpuUseHtml").html().match(/([0-9]+)/)[1]);
    var decimals = new String(to).match(/\.([0-9]+)/) ? new String(to).match(/\.([0-9]+)$/)[1].length : 0;
    new countUp(jQuery("#cpuUseHtml")[0], from, to, decimals, 2.5, opts).start();

      console.log(data[1]);

    memUse.value(data[1]);
    var to = data[1];
    var from = parseInt(jQuery("#memUseHtml").html().match(/([0-9]+)/)[1]);
    var decimals = new String(to).match(/\.([0-9]+)/) ? new String(to).match(/\.([0-9]+)$/)[1].length : 0;
    new countUp(jQuery("#memUseHtml")[0], from, to, decimals, 2.5, opts).start();
  }, "json");
}, 5000);
</script>
