<div class="display task">
	<div class="header">
		<h1>{$pageInfo.title}</h1>
	</div>

	<div class="body">
	<div class="content">
		<div class="row">
			{formlabel label="Issued" for="department"}
			{forminput}
				{$pageInfo.ticket_ref|bit_short_datetime}
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Entered By" for="init_id"}
			{forminput}
				{$pageInfo.creator_real_name|escape} 
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Last updated by" for="staff_id"}
			{forminput}
				{$pageInfo.modifier_real_name|escape} 
			{/forminput}
		</div>
		{form enctype="multipart/form-data" id="editpageform"}
			<input type="hidden" name="content_id" value="{$pageInfo.content_id}" />
			<input type="hidden" name="new_user" value="{$pageInfo.init_id}" />
			{textarea}{$pageInfo.data}{/textarea}
			<div class="row submit">
				<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />&nbsp;
				<input type="submit" name="fSavePage" value="{tr}Save{/tr}" />
			</div>
		{/form}
	</div><!-- end .content -->	</div>
</div> {* end .task *}
