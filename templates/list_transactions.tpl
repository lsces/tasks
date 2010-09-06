		<div class="row">
			{formlabel label="Transactions" for="transaction"}
			{forminput}
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
					{section name=trans loop=$taskInfo.trans}
						<tr class="{cycle values="even,odd"}" title="{$taskInfo[trans].title|escape}">
							<td>
								{$taskInfo.trans[trans].transact}
							</td>
							<td>
								{$taskInfo.trans[trans].staff_name|escape}
							</td>
							<td>
								{$taskInfo.trans[trans].status}
							</td>
							<td>
								{$taskInfo.trans[trans].note}
							</td>
						</tr>
					{/section}
				</tbody>
			</table>
			{/forminput}
		</div>

