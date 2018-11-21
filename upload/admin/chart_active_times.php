<div id="barActiveTimesChart" style="width: 100%; position: relative; height: 300px; z-index: 500;"></div>

<script type="text/javascript">

    jQuery(function($) {
        var dataSeries = [{ label: 'Hours of the day', data: <?php echo json_encode($nFirstReport); ?>}];

        var options = {
            series: {
                lines: { show: true },
                points: { show: true }
            },
            legend: { noColumns: 2 },
            xaxis: { tickDecimals: 0, tickSize: 2 },
            yaxis: { min: 0 },
            selection: { mode: "x" }
        };

        function bota_active_times_chart() {
            var plot = $.plot($("#barActiveTimesChart"), dataSeries, options);
        }

        bota_active_times_chart();

        $(window).resize(function() {
            bota_active_times_chart();
        });
    });

</script>