<?php
/** 
 * The file used to manage chart style 2.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */

    $dataPoints = array();
    foreach($field_values as $key => $value){
        $dataPoints[$key]['name'] = $value->$field;
        $dataPoints[$key]['y'] = $value->total;
    }
?>
<!-- chart style 2 Start-->
<script>
$(document).ready(function() {
// Build the chart
    Highcharts.chart('<?php echo $chart_block_id; ?>', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        credits: {
            enabled: false
        },
        title: {
            text: '<?php echo $module_title; ?>',
            align: 'left',
        },
        tooltip: {
            headerFormat: '',
            pointFormat: '<b> {point.name}</b> : {point.y}'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                cursor: 'pointer',
                borderWidth: 4,
                dataLabels: {
                    enabled: true,
                    distance: -55,
                    format: '{point.y}',
                    style: {
                        fontSize: 15,
                    }
                },
                showInLegend: <?php echo $legend_enable; ?>,
            }
        },
        series: [{
            name: '<?php echo $module_name; ?>',
            data: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        }]
    });
});
</script>
<div class="bcp-chart-2 bcp-block-div">
    <div id = "<?php echo $chart_block_id; ?>" style = "width: 100%; height: 100%; margin: 0 auto"></div>
</div>
<!-- chart style 2 End-->