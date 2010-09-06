<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_tasks/index.php,v 1.11 2010/02/08 21:27:26 wjames5 Exp $
 *
 * Copyright (c) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 *
 * @package tasks
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'tasks' );
include_once( TASKS_PKG_PATH.'Tasks.php' );

$userstate = $gBitUser->getPreference( 'task_process', 0 );
if ( $userstate ) {
	$gTask = new Tasks( null, $userstate );
	$gTask->load();
	if( !empty( $_REQUEST['property_id'] ) ) {
		$updatetask = array();
		$updatetask['new_property'] = $_REQUEST['property_id'];
	 	$gTask->store( $updatetask );
	}
} else {
	$gTask = new Tasks();
}

if ( $gTask->isValid() and $userstate <> 0 ) {
	$gBitSmarty->assign_by_ref( 'userstate', $userstate );
	$gBitSmarty->assign_by_ref( 'taskInfo', $gTask->mInfo );
	$dept_tree = $gTask->listQueues();
	$gBitSmarty->assign_by_ref( 'departments', $dept_tree['depts'] );
	$gBitSmarty->assign_by_ref( 'tags', $dept_tree['tags'] );
	$gBitSmarty->assign_by_ref( 'subtags', $dept_tree['subtags'] );

	require_once( PROPERTY_PKG_PATH.'Property.php');
	$gProperty = new Property( $gTask->mPropertyId, null );
	$gProperty->load();
	if ( $gProperty->isValid() ) {
		$gProperty->loadXrefList();
		$gBitSmarty->assign_by_ref( 'propertyInfo', $gProperty->mInfo );
	}
	$gBitSystem->setBrowserTitle("Task List Item");
	$gBitSystem->display( 'bitpackage:tasks/show_task.tpl', NULL, array( 'display_mode' => 'display' ));
} else {
	header ("location: ".TASKS_PKG_URL."view.php");
	die;
}
?>
