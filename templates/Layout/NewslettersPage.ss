<h1>$Title</h1>
$Content
<% if Newsletters %>
	<ul>
		<% control Newsletters %>
			<li><a href="$Link">$SentDate.Date - $Subject</a></li>
		<% end_control %>
	</ul>
<% end_if %>
$Form