	<div class="content">

		<div class="row">
			{formlabel label="Issued" for="department"}
			{forminput}
				{$taskInfo.ticket_ref|bit_short_time} - {$taskInfo.ticket_id}
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Status" for="status"}
			{forminput}
				{$taskInfo.status} -                                                                    
				{if $taskInfo.room eq 0}Staff List:
					<select name="asign_staff">
						<option value="0" {if $taskInfo.staff_id eq 0 } selected {/if} >Not set
						<option value="1" {if $taskInfo.staff_id eq 1 } selected {/if} >Lester
						<option value="2" {if $taskInfo.staff_id eq 2 } selected {/if} >James
						<option value="3" {if $taskInfo.staff_id eq 3 } selected {/if} >Alan
						<option value="4" {if $taskInfo.staff_id eq 4 } selected {/if} >Roger
						<option value="5" {if $taskInfo.staff_id eq 5 } selected {/if} >Debbie
					</select>
				{elseif $taskInfo.room eq 1}Get Keys .. On Site
				{elseif $taskInfo.room eq 2}On Site
				{elseif $taskInfo.room eq 3}Still on Site .. Left Site
				{elseif $taskInfo.room eq 4}Still on Site .. Left Site
				{elseif $taskInfo.room eq 5}Keys Return .. Finished
				{elseif $taskInfo.room eq 6}Finished
				{/if}
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Entered By" for="staff_id"}
			{forminput}
				{$taskInfo.creator_real_name|escape} 
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Last updated by" for="department"}
			{forminput}
				{$taskInfo.modifier_real_name|escape} 
			{/forminput}
		</div>
		{include file="bitpackage:tasks/list_transactions.tpl"}
	</div><!-- end .content -->
