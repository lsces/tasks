<?php
/**
 * @version      $Header$
 *
 * @author       lsces  <lester@lsces.co.uk>
 * @package      tasks
 * @copyright    2008 bitweaver
 * @license      LGPL {@link http://www.gnu.org/licenses/lgpl.html}
 **/

/**
 * Setup
 */ 
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'tasks' );

require_once( TASKS_PKG_PATH.'Tasks.php');
$gTask = new Tasks();

$offset = 0;
if( !empty( $_REQUEST['type'] ) ) {
	$offset =  ($_REQUEST['type']);
}

$_REQUEST['task_offset'] = $offset;
$gTask->store( $_REQUEST );
$gBitUser->storePreference('task_process', $gTask->mContentId );

// Refresh display
header ("location: ".TASKS_PKG_URL."index.php");
die;
?>