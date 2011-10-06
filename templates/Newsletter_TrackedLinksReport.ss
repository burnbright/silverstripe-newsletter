<h2>Analytics</h2>
<p>date sent: $SentDate.Nice</p>

<% if Stats %>

<div id="chart_div"></div>

<% control Stats %>
<ul>
	<li>
		<strong>$Sent</strong> sent
		<ul style="margin-left:20px;">
			<li>$Opened ($OpenedPercent) opened</li>
			<li>$Unopened ($UnopenedPercent) un-opened</li>
			<li>$Bounced ($BouncedPercent) bounced</li>
		</ul>
	</li>
	<li>$NotSent not sent</li>
	<li>$Unsubscribes un-subscribed</li>
	<li>$Clicks clicked</li>
</ul>
<% end_control %>

<% end_if %>

<h2><% _t('URLCLICKS', 'URL Clicks') %></h2>

<% if NewsletterLinks %>
	<table summary="List of tracked links in this newsletter and number of visits" class="CMSList">
		<thead>
			<tr>
				<th>Link</th>
				<th>Unique Clicks</th>
				<th>Total Clicks</th>
			</tr>
		<thead>
		<tbody>
			<% control NewsletterLinks %>
				<tr>
					<td><a href="$Original" target="blank">$Original</a></td>
					<td>$UniqueVisits</td>
					<td>$Visits</td>
				</tr>
			<% end_control %>
		</tbody>
	</table>
<% else %>
	<p><%  _t('NOTRACKEDLINKS', 'There are no links to monitor') %></p>
<% end_if %>