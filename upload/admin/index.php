<?php
require_once('../track/config.php');

if(!empty($_POST['login'])){
	if(!empty($_POST['username']) AND !empty($_POST['password']) AND $_POST['username']==ADMIN_username AND $_POST['password']==ADMIN_password) {
		include 'dashboard.php';
	}
	else {
		include 'login.php';
	}
}else {
		include 'login.php';
}

function bota_expand_timeline(array $mapData) {

    $newData = array();
	$prevItem[0]='';

    foreach ($mapData as &$item) {

        if ((float)$prevItem[0] != (float)$item[0] - 3600 && (float)$prevItem[0] > strtotime('2 weeks ago')) {
            $newData[] = array(( (float)$prevItem[0] + 3600 ) * 1000, 0);
            $newData[] = array(( (float)$item[0] - 3600 ) * 1000, 0);
        }

        $newData[] = array((float)$item[0] * 1000, (int)$item[1]);
        $prevItem = $item;
    }

    return $newData;

}

function bota_dashboard_widget_active_times() {
    global $wp, $wpdb;

    $sql = 'SELECT HOUR(visited_on) as label, COUNT(*) as data FROM `bot` GROUP BY label;';

    $gFirstReport = $wpdb->get_results($sql, ARRAY_N);
    $nFirstReport = array();
    $lastArrayDate = 0;

    foreach ($gFirstReport as &$item) {
        if ($lastArrayDate != 0 && $lastArrayDate < (int)$item[0] - 1) {
            array_push($nFirstReport, array((int)$item[0] - 1, 0));
        }

        array_push($nFirstReport, array((int)$item[0], (int)$item[1]));

        $lastArrayDate = (int)$item[0];
    }

    include 'chart_active_times.php';
}


function bota_dashboard_widget_top_pages() {
    global $wp, $wpdb;

    $sql = 'SELECT visited_name as label, COUNT(*) AS data FROM `bot`  
        GROUP BY label ORDER BY data DESC LIMIT 10;';

    $gFirstReport = $wpdb->get_results($sql, ARRAY_N);
    $nFirstReport = array();

    foreach ($gFirstReport as &$item) {
        array_push($nFirstReport, array('label' => $item[0], 'data' => (int)$item[1]));
    }

    include 'chart_top_pages.php';
}

function bota_dashboard_widget_top_bots() {
    global $wp, $wpdb;

    $sql = 'SELECT visited_by as label, COUNT(*) AS data FROM `bot`  
        GROUP BY visited_by ORDER BY data DESC LIMIT 10;';

    $gFirstReport = $wpdb->get_results($sql, ARRAY_N);
    $nFirstReport = array();

    foreach ($gFirstReport as &$item) {
        array_push($nFirstReport, array('label' => $item[0], 'data' => (int)$item[1]));
    }

    include 'chart_top_bots.php';
}
function bota_dashboard_widget_function() {
    global $wp, $wpdb,$defaultBots;
    
    $sql = 'SELECT ( UNIX_TIMESTAMP( visited_on ) - UNIX_TIMESTAMP( visited_on ) %% ( 3600 ) ) AS visit_date, COUNT(*) as visit_total FROM `bot`
        WHERE `visited_by` = %s AND `visited_on` > DATE_SUB( NOW( ), INTERVAL 14 DAY) GROUP BY visit_date;';

    $gFirstReport  = $wpdb->get_results($wpdb->prepare($sql, $defaultBots[0]['name']), ARRAY_N);
    $gSecondReport = $wpdb->get_results($wpdb->prepare($sql, $defaultBots[1]['name']), ARRAY_N);
    $gThirdReport  = $wpdb->get_results($wpdb->prepare($sql, $defaultBots[2]['name']), ARRAY_N);
    
    $nFirstReport = array();
    $nSecondReport = array();
    $nThirdReport = array();

    $nFirstReport = bota_expand_timeline($gFirstReport);
    $nSecondReport = bota_expand_timeline($gSecondReport);
    $nThirdReport = bota_expand_timeline($gThirdReport);
    
    $gStartDate = date('Y-m-d', strtotime('14 days ago'));
    $gEndDate = date('Y-m-d', strtotime('tomorrow'));

    include 'dashboard_graph.php';
}

function bota_dashboard_plain_graph_function() {
    global $wp, $wpdb,$defaultBots;
	
    $sql = 'SELECT UNIX_TIMESTAMP(DATE(`visited_on`)) as visit_date, COUNT(`visited_on`) as visit_total FROM `bot`
        WHERE `visited_by` = %s AND `visited_on` > DATE_SUB( NOW( ), INTERVAL 14 DAY) GROUP BY DATE(`visited_on`);';
	
    $gFirstPlainReport  = $wpdb->get_results($wpdb->prepare($sql, $defaultBots[0]['name']), ARRAY_N);
    $gSecondPlainReport = $wpdb->get_results($wpdb->prepare($sql, $defaultBots[1]['name']), ARRAY_N);
    $gThirdPlainReport  = $wpdb->get_results($wpdb->prepare($sql, $defaultBots[2]['name']), ARRAY_N);
    
    foreach ($gFirstPlainReport as &$item) {
        $item[0] = (float)$item[0] * 1000;
        $item[1] = (int)$item[1];
    }
    
    foreach ($gSecondPlainReport as &$item) {
        $item[0] = (float)$item[0] * 1000;
        $item[1] = (int)$item[1];
    }
    
    foreach ($gThirdPlainReport as &$item) {
        $item[0] = (float)$item[0] * 1000;
        $item[1] = (int)$item[1];
    }
    
    $gStartDate = date('Y-m-d', strtotime('14 days ago'));
    $gEndDate = date('Y-m-d', strtotime('tomorrow'));

    include 'dashboard_plain_graph.php';
}

function bota_dashboard_latest_function() {
    global $wp, $wpdb;
    
    $sql = 'SELECT * FROM `bot` ORDER BY `visited_on` DESC LIMIT 500';
    $gFirstReport = $wpdb->get_results($sql);
    
    include 'dashboard_latest.php';
}
?>