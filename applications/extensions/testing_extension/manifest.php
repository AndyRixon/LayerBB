<?php

global $LAYER;
//An example to make use of the template system and MySQL library.

//Running MySQL stuff.
$users = $MYSQL->query('SELECT * FROM {prefix}users');
$return = '<ul>';
foreach ($users as $user) {
    $return .= '<li>' . $user['username'] . '</li>';
}
$return .= '</ul>';

//Adding the data into the template parameter %list_all_users%
$LAYER->tpl->addParam('list_all_users', $return);
?>