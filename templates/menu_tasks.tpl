{* $Header$ *}
{strip}
<ul>
	{if $userstate > 0 }
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}index.php?refer=2">{biticon iname="go-down" iexplain="Arrived at Site" ilocation=menu}</a></li>
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}index.php?refer=3">{biticon iname="go-previous" iexplain="On Site" ilocation=menu}</a></li>
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}index.php?finish=1">{biticon iname="go-up" iexplain="Clear of Site" ilocation=menu}</a></li>
{*			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}find_property.php">{biticon iname="applications-office" iexplain="Find property" ilocation=menu}</a></li>
*}
	{/if}
	{if !$userstate or $userstate eq 0  }
		{if $gBitUser->hasPermission( 'p_tasks_view' )}
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}view.php">{biticon iname="document-new" iexplain="View Patrol List" ilocation=menu}</a></li>
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}view_tickets.php">{biticon iname="document-new" iexplain="View Jobs" ilocation=menu}</a></li>
		{/if}
	
		{if $gBitUser->hasPermission( 'p_tasks_create' )}
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}add_callout.php">{biticon iname="document-print" iexplain="Create Callout" ilocation=menu}</a></li>
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}add_patrol.php">{biticon iname="document-print" iexplain="Add to Patrol" ilocation=menu}</a></li>
			<li><a class="item" title="" href="add_enquiry.php?type=2">{biticon iname="document-new" iexplain="Create Enquiry" ilocation=menu}</a>
				<ul>
					<li><a class="item" title="" href="add_enquiry.php?type=1">{biticon iname="phone" iexplain="Report" ilocation=menu}</a></li>
					<li><a class="item" title="" href="add_enquiry.php?type=2">{biticon iname="phone" iexplain="Telephone" ilocation=menu}</a></li>
					<li><a class="item" title="" href="add_enquiry.php?type=3">{biticon iname="internet-mail" iexplain="eMessage" ilocation=menu}</a></li>
					<li><a class="item" title="" href="add_enquiry.php?type=4">{biticon iname="emblem-mail" iexplain="Mail" ilocation=menu}</a></li>
				</ul>
			</li>
		{/if}
	
		{if $gBitUser->hasPermission( 'p_tasks_supervise' )}
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}logon_list.php">{biticon iname="user" iexplain="Manage Logon" ilocation=menu}</a></li>
		{/if}
	
		{if $gBitUser->hasPermission( 'p_tasks_admin' )}
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}admin_terminals.php">{biticon iname="input-keyboard" iexplain="Admin terminals" ilocation=menu}</a></li>
		{/if}
	{/if}
	{if $userstate < 0 }
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}find_citizen.php">{biticon iname="go-right" iexplain="Find existing citizen" ilocation=menu}</a></li>
			<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}add_citizen.php">{biticon iname="go-right" iexplain="Create new citizen" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}