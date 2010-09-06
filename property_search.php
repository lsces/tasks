<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_tasks/client_search.php,v 1.4 2010/02/08 21:27:26 wjames5 Exp $
 *
 * Copyright (c) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @package client
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'contact' );
include_once( PROPERTY_PKG_PATH.'Property.php' );

$gBitSystem->verifyPermission( 'p_contact_view' );

$userstate = $gBitUser->getPreference( 'task_process', 0 );
if ( $userstate ) {
	if( !empty( $_REQUEST["content_id"] ) ) {
		$gTask = new Tasks( null, $userstate );
		$gTask->load();
		$updatetask = array();
		$updatetask['new_contact'] = $_REQUEST['content_id'];
	 	$gTask->store( $updatetask ); 	
	}
}
$gBitSmarty->assign_by_ref( 'userstate', $userstate );
	
$gContent = new Property( );

if( !empty( $_REQUEST["find_org"] ) ) {
	$_REQUEST["find_name"] = '';
	$_REQUEST["sort_mode"] = 'organisation_asc';
} else if( empty( $_REQUEST["sort_mode"] ) ) {
	$_REQUEST["sort_mode"] = 'surname_asc';
	$_REQUEST["find_name"] = 'a';
}

//$client_type = $gContent->getClientsTypeList();
//$gBitSmarty->assign_by_ref('client_type', $client_type);
$listHash = $_REQUEST;
// Get a list of matching client entries

$listproperties = $gContent->getList( $listHash );
$gBitSmarty->assign_by_ref( 'listproperties', $listproperties );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );

$gBitSystem->setBrowserTitle("View Properties List");
// Display the template
$gBitSystem->display( 'bitpackage:property/list_properties.tpl', NULL, array( 'display_mode' => 'list' ));

?>
