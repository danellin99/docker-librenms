<?php

require 'includes/html/graphs/common.inc.php';

$descr_len = 24;
$float_precision = 2;
$unit_text = 'Deadlocks/sec';
$rrd_filename = Rrd::name($device['hostname'], ['app', 'mssql', $app->app_id]);

$array = [
    'deadlocks_sec' => [
        'descr' => 'Deadlocks',
        'colour' => 'FF0000',
        'area' => true,
    ],
];

$i = 0;
if (Rrd::checkRrdExists($rrd_filename)) {
    foreach ($array as $ds => $var) {
        $rrd_list[$i]['filename'] = $rrd_filename;
        $rrd_list[$i]['descr'] = $var['descr'];
        $rrd_list[$i]['ds'] = $ds;
        $rrd_list[$i]['colour'] = $var['colour'];
        $rrd_list[$i]['area'] = $var['area'];
        $i++;
    }
} else {
    throw new \LibreNMS\Exceptions\RrdGraphException("No Data file $rrd_filename");
}

$nototal = 1;
$colours = 'mixed';

require 'includes/html/graphs/generic_multi_line.inc.php';
