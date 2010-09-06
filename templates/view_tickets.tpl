{* $Header$ *}
{strip}
<div class="display tasks">
	<div class="header">
		<h1>{$currentInfo.title}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		<div class="row">
			<table>
				<caption>{tr}List of Outstanding enquiries{/tr}</caption>
				<thead>
					<tr>
						<th>Ticket</th>
						<th>Date</th>
						<th>Site</th>
						<th>Status</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>
					{section name=ticket loop=$currentInfo.tickets}
						<tr class="{cycle values="even,odd"}" title="{$currentInfo.ticket[ticket].title|escape}">
							<td>
								<a title="View {$currentInfo.tickets[ticket].ticket_id}" href="{$smarty.const.TASKS_PKG_URL}index.php?content_id={$currentInfo.tickets[ticket].ticket_id}">{$currentInfo.tickets[ticket].ticket_id}</a>
							</td>
							<td>
								{$currentInfo.tickets[ticket].ticket_ref|bit_long_datetime}
							</td>
							<td>
								{$currentInfo.tickets[ticket].title|escape}
							</td>
							<td>
								{$currentInfo.tickets[ticket].reason|escape}
							</td>
							<td>
								{$currentInfo.tickets[ticket].note|escape}
							</td>
						</tr>
					{sectionelse}
						<tr class="norecords">
							<td colspan="3">
								{tr}No records found{/tr}
							</td>
						</tr>
					{/section}
				</tbody>
			</table>
		</div>

	</div><!-- end .body -->
</div><!-- end .tasks -->
{/strip}
	