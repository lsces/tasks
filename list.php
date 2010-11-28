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

$currentInfo = array();
$currentInfo['title'] = 'Outstanding Jobs';

$gBitSmarty->assign_by_ref( 'currentInfo', $currentInfo );

// Display the template
$gBitSystem->display( 'bitpackage:tasks/list.tpl', tra( 'Outstanding Jobs' ) , array( 'display_mode' => 'list' ));
?>