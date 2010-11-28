<?php
/**
 * @version $Header$
 *
 * Copyright ( c ) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @package citizen
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyBase.php' );		// Tasks base class

/**
 * @package tasks
 */
class Tasks extends LibertyBase {
	var $mTicketId;
	var $mPropertyId;
	var $mClientId;

	/**
	 * Constructor 
	 * 
	 * Build a Citizen object based on LibertyContent
	 * @param integer Citizen Id identifer
	 * @param integer Base content_id identifier 
	 */
	function Tasks( $pTicketId = NULL, $pContentId = NULL ) {
		BitBase::BitBase();
		$this->mTicketId = (int)$pTicketId;
		$this->mContentId = (int)$pContentId;
		$this->mPropertyId = 0;
		// Permission setup
		$this->mViewContentPerm  = 'p_tasks_view';
		$this->mCreateContentPerm  = 'p_tasks_create';
		$this->mUpdateContentPerm  = 'p_tasks_update';
		$this->mAdminContentPerm = 'p_tasks_admin';
	}

	/**
	 * Load a Task Ticket
	 *
	 * (Describe Task object here )
	 */
	function load($pContentId = NULL) {
		if ( $pContentId ) $this->mContentId = (int)$pContentId;
		if( $this->verifyId( $this->mContentId ) ) {
 			$query = "select ti.*, tag.`title` AS status, tag.`reason_source` AS subtag, tag.`tag` AS tag_abv, tag.`reason`
 				FROM `".BIT_DB_PREFIX."task_ticket` ti
				LEFT JOIN `".BIT_DB_PREFIX."task_reason` tag ON (ti.`room` = tag.`reason`)
				WHERE ti.`ticket_id`=?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );

			if ( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = (int)$result->fields['content_id'];
				$this->mPropertyId = (int)$result->fields['caller_id'];
				$this->mClientId = (int)$result->fields['caller_id'];
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['title'] = 'Ticket - '.$this->mInfo['ticket_id'];
				$this->mInfo['reason'] = $this->mInfo['tag_abv'].' - '.$this->mInfo['reason'];
			}
		}
//		$this->loadTransactionList();
		return;
	}

	/**
	* verify, clean up and prepare data to be stored
	* @param $pParamHash all information that is being stored. will update $pParamHash by reference with fixed array of itmes
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access private
	**/
	function verify( &$pParamHash ) {
		if ( !empty( $pParamHash['property'] ) ) {
			$pParamHash['caller_id'] = $pParamHash['property'];
		} else {
			$pParamHash['caller_id'] = 0;
		}
			
		if ( !empty( $pParamHash['patrol'] ) ) {
			$pParamHash['ticket_ref'] = $pParamHash['patrol'];
		} else {
			$pParamHash['ticket_ref'] = $this->mDb->NOW();
		}
			
		// Secondary store entries
/*		if( $this->isValid() ) {
			if ( !empty( $pParamHash['new_client'] ) ) {
				$pParamHash['task_store']['caller_id'] = $pParamHash['new_client'];
				$pParamHash['task_store']['usn'] = $pParamHash['new_client'];
			}
			if ( !empty( $pParamHash['new_property'] ) ) {
				$pParamHash['task_store']['caller_id'] = $pParamHash['new_property'];
				$pParamHash['task_store']['usn'] = $pParamHash['new_property'];
			}
			if ( !empty( $pParamHash['new_dept'] ) ) {
				$pParamHash['task_store']['department'] = $pParamHash['new_dept'];
			}
			if ( !empty( $pParamHash['new_user'] ) ) {
				$pParamHash['task_store']['staff_id'] = $pParamHash['new_user'];
			}
			if ( !empty( $pParamHash['new_room'] ) ) {
				$pParamHash['task_store']['room'] = $pParamHash['new_room'];
				// Add transaction table insert here to replace database trigger
			}
			if ( !empty( $pParamHash['new_tag'] ) ) {
				$pParamHash['task_store']['tags'] = $pParamHash['new_tag'];
				// Add transaction table insert here to replace database trigger
			}
		}
*/
				return( count( $this->mErrors ) == 0 );
	}

	/**
	* Store task data
	* @param $pParamHash contains all data to store the task ticket
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	**/
	function store( &$pParamHash ) {
		if( $this->verify( $pParamHash ) ) {
			// Start a transaction wrapping the whole insert into liberty 

			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."task_ticket";
			if( !empty( $pParamHash['task_store'] ) ) {
					$result = $this->mDb->associateUpdate( $table, $pParamHash['task_store'], array( "ticket_id" => $this->mContentId ) );
			} else {
				global $gBitUser;

				$pParamHash['task_store']['content_id'] = $pParamHash['content_id'];
				$pParamHash['task_store']['ticket_id'] = $this->mDb->GenID( 'contact_xref_seq' );
				$pParamHash['task_store']['ticket_ref'] = $pParamHash['ticket_ref'];
				$pParamHash['task_store']['last'] = $this->mDb->NOW();
				$pParamHash['task_store']['ticket_no'] = $pParamHash['content_id'];
				$pParamHash['task_store']['office'] = 1;
				$pParamHash['task_store']['staff_id'] = 0;
				$pParamHash['task_store']['init_id'] = $gBitUser->mUserId;
				$pParamHash['task_store']['caller_id'] = $pParamHash['caller_id'];
				$pParamHash['task_store']['department'] = $pParamHash['task_offset'];
			
				$this->mContentId = $pParamHash['content_id'];
				$result = $this->mDb->associateInsert( $table, $pParamHash['task_store'] );
			}
			// load before completing transaction as firebird isolates results
			$this->load();
			$this->mDb->CompleteTrans();
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * Delete content object and all related records
	 */
	function expunge()
	{
		$ret = FALSE;
		if ($this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."tasks_ticket` WHERE `ticket_id` = ?";
			$result = $this->mDb->query($query, array($this->mContentId ) );
			$query = "DELETE FROM `".BIT_DB_PREFIX."tasks_transactions` WHERE `ticket_id` = ?";
			$result = $this->mDb->query($query, array($this->mContentId ) );
			$this->mDb->CompleteTrans();
			$ret = TRUE;
		}
		return $ret;
	}
    
	/**
	 * Returns Request_URI to a Task content object
	 *
	 * @param string name of
	 * @param array different possibilities depending on derived class
	 * @return string the link to display the page.
	 */
	function getDisplayUrl( $pContentId=NULL ) {
		global $gBitSystem;
		if( empty( $pContentId ) ) {
			$pContentId = $this->mContentId;
		}

		return TASKS_PKG_URL.'index.php?content_id='.$pContentId;
	}

	/**
	 * Returns HTML link to display a Task object
	 * 
	 * @param string Not used ( generated locally )
	 * @param array mInfo style array of content information
	 * @return the link to display the page.
	 */
	function getDisplayLink( $pText, $aux ) {
		if ( $this->mContentId != $aux['content_id'] ) $this->load($aux['content_id']);

		if (empty($this->mInfo['content_id']) ) {
			$ret = '<a href="'.$this->getDisplayUrl($aux['content_id']).'">'.$aux['title'].'</a>';
		} else {
			$ret = '<a href="'.$this->getDisplayUrl($aux['content_id']).'">'."Citizen - ".$this->mInfo['title'].'</a>';
		}
		return $ret;
	}

	/**
	 * Returns title of an Task object
	 * @todo Need to expand this to handle type of task and date information
	 *
	 * @param array mInfo style array of content information
	 * @return string Text for the title description
	 */
	function getTitle( $pHash = NULL ) {
		$ret = NULL;
		if( empty( $pHash ) ) {
			$pHash = &$this->mInfo;
		} else {
			if ( $this->mContentId != $pHash['content_id'] ) {
				$this->load($pHash['content_id']);
				$pHash = &$this->mInfo;
			}
		}

		if( !empty( $pHash['title'] ) ) {
			$ret = "Ticket - ".$this->mInfo['title'];
		} elseif( !empty( $pHash['content_name'] ) ) {
			$ret = $pHash['content_name'];
		}
		return $ret;
	}

	/**
	 * Returns title of a queue 
	 * @todo Need to cache department/queue information in object
	 *
	 * @param integer Queue Number
	 * @return string Text for the title description
	 */
	function getQueueTitle( $queue ) {
		$query = "SELECT rs.`title` AS queue FROM `".BIT_DB_PREFIX."task_roomstat` rs WHERE rs.`terminal` = 80 + $queue";
		return $this->mDb->getOne( $query );
	}
	
	/**
	 * Gets the next ticket number from a queue 
	 *
	 * @param integer Queue Number
	 * @return bool True if switched to a valid task
	 */
	function getNextTask( $queue ) {
		$query = "SELECT cd.`ticket_id` FROM  `".BIT_DB_PREFIX."task_ticket` cd
					WHERE cd.`ticket_ref` BETWEEN 'TODAY' AND 'TOMORROW' AND cd.`room` = $queue + 80
					AND cd.`office` = 1
				  	ORDER BY cd.`ticket_ref`";
		$next = $this->mDb->getOne( $query );
// Add switch of user state to serving!
		if ( $next ) return true;
		else return false;
	}
	
	/**
	 * Returns title of a queue 
	 * @todo Need to cache department/queue information in object
	 *
	 * @param integer Queue Number
	 * @return string Text for the title description
	 */
	function createTask( $queue ) {
		$query = "SELECT cd.`ticket_id` FROM  `".BIT_DB_PREFIX."task_ticket` cd
						 WHERE ti.`ticket_ref` BETWEEN TODAY AND TOMORROW AND cd.`room` = $queue + 80
						 AND cd.`office` = 1
				  		 ORDER BY cd.`ticket_ref`";
		$next = $this->mDb->getOne( $query );
// Add switch of user state to serving!
		if ( $next ) return true;
		else return false;
	}
	
	/**
	 * Returns list of tesk entries
	 *
	 * @param integer 
	 * @return array Enquiry tickets
	 */
	function getList( &$pListHash ) {
		LibertyContent::prepGetList( $pListHash );
		
		$whereSql = $joinSql = $selectSql = '';
		$bindVars = array();
		array_push( $bindVars, $pListHash['content_id'] );
// Update to more flexible date management later
//		array_push( $bindVars, 'TODAY' );
//		array_push( $bindVars, 'TOMORROW' );
//		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		$query = "SELECT ti.*, tr.`title` as reason
				FROM `".BIT_DB_PREFIX."task_ticket` ti 
				LEFT JOIN `".BIT_DB_PREFIX."task_reason` tr ON (tr.`reason` = ti.`room`)
				$joinSql
				WHERE ti.`content_id` = ? $whereSql  
				order by ti.`ticket_ref`";
		$query_cant = "SELECT COUNT(ti.`ticket_no`) FROM `".BIT_DB_PREFIX."task_ticket` ti
				$joinSql
				WHERE ti.`content_id` = ? $whereSql";

		$ret = array();
		$this->mDb->StartTrans();
		$result = $this->mDb->query( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );
		$cant = $this->mDb->getOne( $query_cant, $bindVars );
		$this->mDb->CompleteTrans();

		while ($res = $result->fetchRow()) {
			$res['ticket_url'] = $this->getDisplayUrl( $res['ticket_id'] );
			$ret[] = $res;
		}

		$pListHash['cant'] = $cant;
		LibertyContent::postGetList( $pListHash );
		return $ret;
	}
	
	/**
	 * Returns list of queues
	 *
	 * @param integer 
	 * @return array Queue records
	 */
	function listQueues() {
		$query = "SELECT rs.`terminal` - 81, (rs.`terminal` - 80) AS que_no, rs.`title`, rs.`ter_type` AS dep_type
			FROM `".BIT_DB_PREFIX."task_roomstat` rs
			WHERE rs.`ter_type` > 6 AND rs.`terminal` > 80
			ORDER BY rs.`terminal`";
		$result = array();
		$result['depts'] = $this->mDb->GetAssoc( $query );
		if ( isset($this->mInfo['department']) && $this->mInfo['department'] > 0 ) {
			$result['tags']	= $this->mDb->GetAssoc("SELECT `reason`, `reason` AS tag_no, `title`, `tag` FROM `".BIT_DB_PREFIX."task_reason` WHERE `reason_type` = ".$this->mInfo['department']." ORDER BY `reason`");
			if ( $this->mInfo['subtag'] > 0 ) {
				$result['subtags'] = $this->mDb->GetAssoc("SELECT `reason`, `reason` AS tag_no, `title`, `tag` FROM `".BIT_DB_PREFIX."task_reason` WHERE `reason_type` = ".$this->mInfo['subtag']." ORDER BY `reason`");
			}
		}
		return $result;
	}
	
	/**
	 * Returns list of queue activity
	 *
	 * @param integer 
	 * @return array Queue activity records
	 */
	function getQueueList( &$pListHash = NULL ) {
		$query = "SELECT rs.`office`, rs.`terminal`, rs.`title`, rs.`ter_type`, rs.`x1` AS no_warn, rs.`x2` AS no_alarm, rs.`x3` AS aw_warn, rs.`x4` AS aw_alarm,
			COUNT(tic.`ticket_ref`) AS `no_waiting`,
			AVG(((CURRENT_TIMESTAMP - tic.`last`) * 86400)) AS `avg_wait`
			FROM `".BIT_DB_PREFIX."task_roomstat` rs
			LEFT JOIN `".BIT_DB_PREFIX."task_ticket` tic ON tic.`office` = rs.`office` AND tic.`room` = rs.`terminal` AND tic.`ticket_ref` BETWEEN CURRENT_DATE AND CURRENT_DATE + 1
			WHERE rs.`ter_type` > 6 AND rs.`terminal` > 80
			GROUP BY rs.`office`, rs.`terminal`, rs.`title`, rs.`ter_type`, rs.`x1`, rs.`x2`, rs.`x3`, rs.`x4`
			ORDER BY rs.`terminal`";

		$result = $this->mDb->query( $query );
		while ($res = $result->fetchRow()) {
			$res['queue_id'] = $res['terminal'] - 80;
			$res['display_url'] = TASKS_PKG_URL.'view_queue.php?queue_id='.$res['queue_id'];
			$ret[] = $res;
		}
		return $ret;
	}
	
	/**
	 * Returns list of patrol activity
	 *
	 * @param integer 
	 * @return array patrol activity records
	 */
	function getPatrolList( &$pListHash = NULL ) {
		$query = "SELECT tic.*, r.title AS patrol, lc.*
			FROM `".BIT_DB_PREFIX."task_ticket` tic
			LEFT JOIN `".BIT_DB_PREFIX."task_reason` r ON r.`reason` = tic.`room` 
			LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.`content_id` = tic.`caller_id` 
			WHERE tic.`room` = 1 AND tic.`ticket_ref` BETWEEN CURRENT_DATE AND CURRENT_DATE + 1
			ORDER BY tic.`ticket_ref`";

		$result = $this->mDb->query( $query );
		while ($res = $result->fetchRow()) {
			$res['display_url'] = CONTACT_PKG_URL.'view.php?content_id='.$res['caller_id'];
			$ret[] = $res;
		}
		return $ret;
	}

	/**
	 * loadTransactionList( &$pParamHash );
	 * Get list of transaction records relating to the active ticket
	 */
	function loadTransactionList() {
//		if( $this->isValid() ) {
		
			$sql = "SELECT tran.*, tag.`title` AS status, sn.`real_name` AS staff_name 
				FROM `".BIT_DB_PREFIX."task_transaction` tran
				LEFT JOIN `".BIT_DB_PREFIX."task_reason` tag ON (tran.`room` = tag.`reason`)
				LEFT JOIN `".BIT_DB_PREFIX."users_users` sn ON (sn.`user_id` = tran.`staff_id`)
				WHERE tran.ticket_id = ?
				ORDER BY tran.`transact_no`";

			$result = $this->mDb->query( $sql, array( $this->mContentId ) );

			while( $res = $result->fetchRow() ) {
				$this->mInfo['trans'][$res['transact_no']] = $res;
			}
//		}
	}
	
	function hasViewPermission( $pVerifyAccessControl ) { return FALSE; }
}
?>
