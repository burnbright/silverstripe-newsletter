<% if Entries %>
<table class="CMSList UnsubscribedList" summary="Unsubscribed users">
	<thead>
		<tr>
            <th><% _t('UNAME','User name') %></th>
            <th><% _t('UNSUBON','Unsubscribed on') %></th>
            <th></th>
        </tr>
	</thead>
    <tbody>
        <% control Entries %>
        <tr>
            <td>$Member.FirstName $Member.Surname</td>
            <td>$Created.Long</td>
            <td><a href="$ResubscribeLink"><% _t('RESUB','re-subscribe') %></a></td>
        </tr>
        <% end_control %>
    </tbody>
</table>
<% else %>
<p>
    <% _t('NOUNSUB','No users have unsubscribed from this newsletter.') %>

</p>
<% end_if %>
