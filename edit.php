<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_wiki/edit.php,v 1.63 2010/02/08 21:27:27 wjames5 Exp $
 *
 * Copyright( c ) 2004 bitweaver.org
 * Copyright( c ) 2003 tikwiki.org
 * Copyright( c ) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id: edit.php,v 1.63 2010/02/08 21:27:27 wjames5 Exp $
 * @package tasks
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );
include_once( TASKS_PKG_PATH.'Tasks.php' );

$gBitSystem->verifyPackage( 'tasks' );

if( !empty( $_REQUEST['content_id'] ) ) {
	$gContent = new Tasks( null, $_REQUEST['content_id'] );
	$gContent->load();
} else {
	$gContent = new Tasks();
}
// Disable parsing data if not asking to preview page
$_REQUEST["parse"] = false;

if( !empty( $gContent->mInfo ) ) {
	$formInfo = $gContent->mInfo;
	$data_to_edit = !empty( $gContent->mInfo['data'] ) ? $gContent->mInfo['data'] : '';
	if (!empty($_REQUEST['section'])) {
		$section = $_REQUEST['section'];
		$data_to_edit = extract_section($data_to_edit,$section);
		$formInfo['data'] = $data_to_edit;
		$formInfo['edit_section'] = 1;
		$formInfo['section'] = $_REQUEST['section'];
	}

	$formInfo['edit'] = $data_to_edit;
	$formInfo['edit_comment'] = '';
}

if( isset( $_REQUEST["edit"] ) ) {
	$formInfo['edit'] = $_REQUEST["edit"];
}

$cat_obj_type = BITPAGE_CONTENT_TYPE_GUID;

// Pro
// Check if the page has changed
if( isset( $_REQUEST["fCancel"] ) ) {
	if( @BitBase::verifyId( $gContent->mContentId ) ) {
		header( "Location: ".$gContent->getDisplayUrl() );
	} else {
		header( "Location: ".TASKS_PKG_URL );
	}
	die;
} elseif( isset( $_REQUEST["fSavePage"] ) ) {

	$data_to_parse = $formInfo['edit'];
	if (!empty($formInfo['section']) && !empty($gContent->mInfo['data']) ) {
		$full_page_data = $gContent->mInfo['data'];
		$data_to_parse = replace_section($full_page_data,$formInfo['section'],$formInfo['edit']);
		$_REQUEST["edit"] = $data_to_parse;
	}

	if( $gContent->store( $_REQUEST ) ) {
		if( $gBitSystem->isFeatureActive( 'wiki_watch_author' ) ) {
			$gBitUser->storeWatch( "wiki_page_changed", $gContent->mPageId, $gContent->mContentTypeGuid, $_REQUEST['title'], $gContent->getDisplayUrl() );
		}

		header( "Location: ".$gContent->getDisplayUrl() );
		die;

	} else {
		$formInfo = $_REQUEST;
		$formInfo['data'] = &$_REQUEST['edit'];
	}
} elseif( !empty( $_REQUEST['edit'] ) ) {
	// perhaps we have a javascript non-saving form submit
	$formInfo = $_REQUEST;
	$formInfo['data'] = &$_REQUEST['edit'];
}

if( isset( $_REQUEST['format_guid'] ) && !isset( $gContent->mInfo['format_guid'] ) ) {
	$formInfo['format_guid'] = $gContent->mInfo['format_guid'] = $_REQUEST['format_guid'];
}

// Flag for 'page bar' that currently 'Edit' mode active
// so no need to show comments & attachments, but need
// to show 'wiki quick help'
$gBitSmarty->assign( 'edit_page', 'y' );

// formInfo might be set due to a error on submit
if( empty( $formInfo ) ) {
	$formInfo = &$gContent->mInfo;
}

if ( $gContent->mContentId ) {
	header ("location: ".TASKS_PKG_URL."list.php");
	die;
}

// make original page title available for template
$formInfo['original_title'] =( !empty( $gContent->mInfo['title'] ) ) ? $gContent->mInfo['title']  : "" ;

$gBitSmarty->assign_by_ref( 'pageInfo', $formInfo );
$gBitSmarty->assign_by_ref( 'gContent', $gContent );
$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );

$gBitSystem->display( 'bitpackage:tasks/edit.tpl', 'Edit: '.$gContent->mInfo['title'], array( 'display_mode' => 'edit' ));