<% if Newsletters %>
		<% control Newsletters %>
			<h2><a href="$Link">$Subject</a></h2>
			<p>$SentDate.Format(F Y)</p>
			<% if Last %><% else %><hr/><% end_if %>
		<% end_control %>
<% else %>
	<p><% _t('NONEWSLETTERS','There are currently no published newsletters') %>.</p>
<% end_if %>