<div class="row">
	<table>
		<thead>
			<tr>
				<th>Time</th>
				<th>By</th>
				<th>Status</th>
				<th>Note</th>
			</tr>
		</thead>
		<tbody>
			{section name=task loop=$taskInfo.tasks}
				<tr class="{cycle values="even,odd"}" title="{$taskInfo[task].title|escape}">
					<td>
						{$taskInfo.tasks[task].ticket_ref}
					</td>
					<td>
						{$taskInfo.tasks[task].staff_name|escape}
					</td>
					<td>
						{$taskInfo.tasks[task].status}
					</td>
					<td>
						{$taskInfo.tasks[task].note}
					</td>
				</tr>
			{/section}
		</tbody>
	</table>
</div>

