<div id="botPlainChart" style="width: 100%; position: relative; height: 400px; z-index: 500;"></div>

<script type="text/javascript">
    jQuery(function($) {
        var firstPlainReport = <?php echo json_encode($gFirstPlainReport); ?>;
        var secondPlainReport = <?php echo json_encode($gSecondPlainReport); ?>;
        var thirdPlainReport = <?php echo json_encode($gThirdPlainReport); ?>;
        
        var options = {
            xaxis: {
                mode: "time", 
                min: new Date("<?php echo $gStartDate; ?>").getTime(), 
                max: new Date("<?php echo $gEndDate; ?>").getTime()
            }
        }

        function bota_plain_graph() {

            var plot = $.plot($("#botPlainChart"), [
                { data: firstPlainReport, label: "<?php echo $defaultBots[0]['name']; ?>", lines: { show: true, fill: true }, points: { show: true } },
                { data: secondPlainReport, label: "<?php echo $defaultBots[1]['name']; ?>", lines: { show: true, fill: true }, points: { show: true } },
                { data: thirdPlainReport, label: "<?php echo $defaultBots[2]['name']; ?>", lines: { show: true, fill: true }, points: { show: true } }
            ], options);

        }
        
        bota_plain_graph();

        $(window).resize(function() {
            bota_plain_graph();
        });
    });
</script>