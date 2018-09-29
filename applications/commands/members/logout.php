<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

/*
 * Sign Out module for LayerBB
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

if (!$LAYER->sess->isLogged) {
    redirect(SITE_URL);
} //If user is not logged in.

$LAYER->sess->remove();

redirect(SITE_URL);

?>