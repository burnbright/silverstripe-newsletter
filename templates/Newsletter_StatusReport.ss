<% if SentRecipients %>
	<h2><% _t('SENTRECIPIENTS','Status of sent emails') %></h2>
	<table class="CMSList">
		<thead>
			<tr>
				<th><% _t('STATUS','Status') %></th>
				<th class="FirstName"><% _t('FN','Firstname') %></th>
				<th class="Surname"><% _t('SN','Surname') %></th>
				<th class="Email" style="width:33%"><% _t('EMAIL','Email') %></th>
				<th><% _t('SENT','Sent') %></th>
				
				<th><% _t('FIRSTOPENED','First Opened') %></th>
				<th><% _t('LASTOPENED','Last Opened') %></th>
				<th><% _t('AGENT','Agent') %></th>
			</tr>
		</thead>
		
		<tbody>
			<% control SentRecipients %>
			<tr id="sent-member-$ID" class="$Status">
				<td class="status">$Status</td>
				<td class='firstname"'>$Member.FirstName</td>
				<td class="surname">$Member.Surname</td>
				<td class="email">$Email</td>
				<td class="sent">$Created</td>
				<td class="firstopened">$FirstOpened</td>
				<td class="lastopened">$LastOpened</td>
				<td class="agent"><% if Agent %><div class="relative">hover to see..<div class="popout">$Agent</div></div><% end_if %></td>
			</tr>
			<% end_control %>
		</tbody>
	</table>
<% end_if %>


<% if UnsentSubscribers %>
	<h2><% _t('NEWSNEVERSENT','The newsletter has never been sent to the following subscribers') %></h2>
	<table class="CMSList">
		<thead>
			<tr>
				<th class="FirstName"><% _t('FN','Firstname') %></th>
				<th class="Surname"><% _t('SN','Surname') %></th>
				<th class="Email" style="width:33%"><% _t('EMAIL','Email') %></th>
			</tr>
		</thead>
		<tbody>
			<% control UnsentSubscribers %>
			<tr id="unsent-member-$ID">
				<td>$FirstName</td>
				<td>$Surname</td>
				<td>$Email</td>
			</tr>
			<% end_control %>
			
		</tbody>
	</table>
<% end_if %>