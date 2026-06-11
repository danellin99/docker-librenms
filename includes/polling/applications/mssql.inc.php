<?php
use LibreNMS\RRD\RrdDefinition;

$name = 'mssql';

if (empty($app_raw) && isset($agent_data['mssql'])) {
    $app_raw = $agent_data['mssql'];
}

$mssql_metrics = [];
$lines = preg_split('/\r\n|\r|\n/', (string)$app_raw);

foreach ($lines as $line) {
    if (strpos($line, ':') !== false) {
        list($key, $val) = explode(':', $line, 2);
        $clean_key = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '_', $key)));
        if ($clean_key == 'state') continue;
        $clean_val = trim($val);
        if (is_numeric($clean_val)) {
             $mssql_metrics[$clean_key] = $clean_val;
        }
    }
}

if (!empty($mssql_metrics)) {
    $rrd_def = RrdDefinition::make();
    foreach ($mssql_metrics as $metric => $value) {
        $ds_type = ($metric === 'deadlocks_sec') ? 'DERIVE' : 'GAUGE';
        $rrd_def->addDataset($metric, $ds_type, 0, 1000000);
    }
    
    $tags = [
        'name' => $name,
        'app_id' => $app->app_id,
        'rrd_name' => ['app', $name, $app->app_id],
        'rrd_def' => $rrd_def,
    ];
    
    app('Datastore')->put($device, 'app', $tags, $mssql_metrics);
    update_application($app, (string)$app_raw, $mssql_metrics);
} else {
    update_application($app, (string)$app_raw, [], 'No numeric data found in mssql section');
}
