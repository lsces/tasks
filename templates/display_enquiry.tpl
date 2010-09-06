	<div class="content">
		<div class="floaticon">
				<a title="{tr}Edit{/tr}" href="edit_enquiry.php?content_id={$taskInfo.content_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="Edit Enquiry"}</a>
		</div>
		<div class="row">
			{formlabel label="Issued" for="department"}
			{forminput}
				{$taskInfo.ticket_ref|bit_short_datetime}
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Entered By" for="init_id"}
			{forminput}
				{$taskInfo.creator_real_name|escape} 
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Last updated by" for="staff_id"}
			{forminput}
				{$taskInfo.modifier_real_name|escape} 
			{/forminput}
		</div>
		<div class="row">
			{forminput}
				{$taskInfo.parsed_data}
			{/forminput}
		</div>
		<div class="clear"></div>
	</div><!-- end .content -->
