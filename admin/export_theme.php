<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

define('BASEPATH', 'Staff');
require_once('../applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    redirect(SITE_URL);
}

header('Content-Type: application/json');

if ($PGET->g('theme')) {

    $MYSQL->bind('id', $PGET->g('theme'));
    $query = $MYSQL->query("SELECT * FROM {prefix}themes WHERE id = :id");

    if( $query ) {

        header('Content-Disposition: attachment; filename="' . $query['0']['theme_name'] . '.json"');

        $data   = json_decode($query['0']['theme_json_data'], true);

        $output = array(
            'theme_name' => $query['0']['theme_name'],
            'theme_version' => $query['0']['theme_version'],
            'templates' => $data['templates'],
            'entities' => $data['entities'],
            'buttons' => $data['buttons']
        );


    } else {
        $output = array(
            'Theme does not exist.'
        );
    }

    echo json_encode($output, JSON_PRETTY_PRINT);

} else {
    $output = array(
        'Please specify a theme.'
    );

    echo json_encode($output, JSON_PRETTY_PRINT);
}

?>
