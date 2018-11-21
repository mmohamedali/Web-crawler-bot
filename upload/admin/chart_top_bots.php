<div id="barTopBotsChart" style="width: 100%; position: relative; height: 300px; z-index: 500;"></div>

<script type="text/javascript">
    jQuery(function($) {
        var dataSeries = <?php echo json_encode($nFirstReport); ?>;
        
        var options = {
            series: {
            pie: { 
                show: true,
                 combine: {
                    color: '#999',
                    threshold: 0.05
                },
                label: {
                    show: true,
                    radius: 2/3,
                    formatter: function(label, series){
                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+Math.round(series.percent)+'%</div>';
                    },
                    background: { opacity: 0.8 }
                }
            }
            },
            grid: {
                hoverable: true,
                clickable: true
            }
        };

        function bota_top_bots_chart() {
            var plot = $.plot($("#barTopBotsChart"), dataSeries, options);
        }
        
        bota_top_bots_chart();
        
        $(window).resize(function() {
            bota_top_bots_chart();
        });
    });
</script>