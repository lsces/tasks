<div class="display task">
	<div class="header">
		<h1>{$taskInfo.title}</h1>
	</div>

	<div class="body">
		{if $taskInfo and $taskInfo.department eq 0 }
				{include file="bitpackage:tasks/display_task.tpl"}
			{if $propertyInfo or $backoffice }
				{include file="bitpackage:tasks/task_survey.tpl"}
			{/if}
		{elseif $taskInfo and $taskInfo.department gt 0 }

			{include file="bitpackage:tasks/display_enquiry.tpl"}
		{/if}

		{if $clientInfo}
			{include file="bitpackage:contact/contact_header.tpl"}
			{include file="bitpackage:contact/contact_date_bar.tpl"}
			{include file="bitpackage:contact/display_contact.tpl"}
		{elseif $propertyInfo}
			{include file="bitpackage:property/property_header.tpl"}
			{include file="bitpackage:property/property_date_bar.tpl"}
			{include file="bitpackage:property/display_property.tpl"}
		{else}
			{include file="bitpackage:tasks/property_search.tpl"}
		{/if}
	</div>
</div> {* end .task *}
