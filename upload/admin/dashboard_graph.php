<div id="botChart" style="width: 100%; position: relative; height: 400px; z-index: 500;"></div>
<div id="botOverview" style="margin: 20px auto 0 auto; width: 90%; height: 50px;"></div>

<script type="text/javascript">
    jQuery(function($) {

        var firstReport = <?php echo json_encode($nFirstReport); ?>;
        var secondReport = <?php echo json_encode($nSecondReport); ?>;
        var thirdReport = <?php echo json_encode($nThirdReport); ?>;
        
        var options = {
            xaxis: {
                mode: "time"
            },
            selection: { mode: "x" },
            legend: { show: true }
        }
        
        function bota_dashboard_graph() {
        
            var dataSeries = [
                { data: firstReport, label: "<?php echo $defaultBots[0]['name']; ?>", lines: { show: true, fill: true }, points: { show: true} },
                { data: secondReport, label: "<?php echo $defaultBots[1]['name']; ?>", lines: { show: true, fill: true }, points: { show: true} },
                { data: thirdReport, label: "<?php echo $defaultBots[2]['name']; ?>", lines: { show: true, fill: true }, points: { show: true} }
            ];
        
            var plot = $.plot($("#botChart"), dataSeries, options);
            
            var overview = $.plot($("#botOverview"), [firstReport, secondReport, thirdReport], {
                series: {
                    lines: { show: true, lineWidth: 1 },
                    shadowSize: 0
                },
                xaxis: { ticks: [], mode: "time" },
                yaxis: { ticks: [], min: 0, autoscaleMargin: 0.1 },
                selection: { mode: "x" }
            });
            
            $("#botChart").bind("plotselected", function (event, ranges) {

                plot = $.plot($("#botChart"), dataSeries,
                $.extend(true, {}, options, {
                    xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                }));

                overview.setSelection(ranges, true);
            });

            $("#botOverview").bind("plotselected", function (event, ranges) {
                plot.setSelection(ranges);
            });
        }

        bota_dashboard_graph();

        $(window).resize(function() { bota_dashboard_graph() });

    });
</script>