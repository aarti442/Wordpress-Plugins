<?php
/** 
 * The file used to manage chart style 1.
 * 
 * LICENSE: The contents of this file are subject to the license agreement ("License") which is included in the installation package (LICENSE.txt). By installing or using this file, you have unconditionally agreed to the terms and conditions of the License, and you may not use this file except in compliance with the License. 
 * 
 * @author Biztech Consultancy
 */

$dataPoints = array();
$rend_number = '500';
foreach ($field_values as $key => $value) {
    $rend = mt_rand(200, 250);
    $dataPoints[$key]['name'] = $value->$field;
    $dataPoints[$key]['y'] = $value->total;
    $dataPoints[$key]['z'] = $rend_number;
    $rend_number += $rend;
}
?>
<!-- chart style 1 Start-->
<script>
    $(document).ready(function () {
        Highcharts.chart('<?php echo $chart_block_id; ?>', {
            chart: {
                type: 'variablepie',

            },
            credits: {
                enabled: false
            },
            title: {
                text: '<?php echo str_replace(' ', '<br/>', $module_title); ?>',
                align: 'center',
                verticalAlign: 'middle',
                y: -10,
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

            series: [{
                    cursor: 'pointer',
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        distance: -40,
                        format: '{point.y}',
                        style: {
                            fontWeight: 'bold',
                            color: '#000',
                            fontSize: 12,
                        }

                    },
                    showInLegend: <?php echo $legend_enable; ?>,
                    innerSize: '38%',
                    zMin: '0',
                    name: '<?php echo $module_name; ?>',
                    data: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
        });

    });

</script>

<div class="bcp-chart-1 bcp-block-div">
    <div id = "<?php echo $chart_block_id; ?>" style = "width: 100%; height: 100%; margin: 0 auto"></div>
</div>
<!-- chart style 1 End-->