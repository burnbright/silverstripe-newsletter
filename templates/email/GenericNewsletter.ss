<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <% base_tag %>
    $MetaTags
    <% if Subject %><title>$Subject</title><% end_if %>
    <style type="text/css">
    
        /* Client-specific Styles */
        #outlook a { padding: 0; } /* Force Outlook to provide a "view in browser" button. */
        body { width: 100% !important; } .ReadMsgBody { width: 100%; } .ExternalClass { width: 100%; } /* Force Hotmail to display emails at full width */
        body { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; } /* Prevent Webkit and Windows Mobile platforms from changing default font sizes. */

        /* Reset Styles */
        body { margin: 0; padding: 0; }
        img { height: auto; line-height: 100%; outline: none; text-decoration: none; display:block; }
        #backgroundTable { height: 100% !important; margin: 0; padding: 0; width: 100% !important; }
    
       p {
           margin: 1em 0;
       }
       
       h1, h2, h3, h4, h5, h6 {
           color: black !important;
           line-height: 100% !important;
       }
       
       h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
           color: blue !important;
       }
       
       h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
           color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
       }
       
       h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
           color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
       }
       
       table td {
           border-collapse: collapse;
       }

       .yshortcuts, .yshortcuts a, .yshortcuts a:link,.yshortcuts a:visited, .yshortcuts a:hover, .yshortcuts a span { color: black; text-decoration: none !important; border-bottom: none !important; background: none !important; } /* Body text color for the New Yahoo.  This example sets the font of Yahoo's Shortcuts to black. */
       
	    .typography img {
			border: none;
		}
			.typography img.right,
			.typography div.right {
				float: right;
				margin-left: 1em;
			}
			.typography img.left,
			.typography div.left  {
				float: left;
				margin-right: 1em;
			}
			.typography img.leftAlone {
				float: left;
				margin-right: 100%;
			}
			.typography img.center {
				float: none;
				margin-left: auto;
				margin-right: auto;
				display: block;
			}
			
			.typography .captionImage .caption{
				text-align:center;
				margin:0 !important;
			}
       
	</style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Verdana,Tahoma,Arial;font-size:12px;margin:0 auto;padding:0;">
	<center>
    <table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" id="backgroundTable"><tr><td align="center" valign="top">
    
	    <table cellpadding="10" cellspacing="0" border="0" width="600" id="templateContainer">
	    	<tbody>
		        <tr>
		            <td align="left" class="typography">
		            	$Body
		            </td>
		        </tr>
	        </tbody>
			<tfoot>
				<% if UnsubscribeLink %>
					<tr><td align="left" class="unsubscribe typography">
						<% _t('TOUNSUBSCRIBE','To unsubscribe from this newsletter') %> <a href="$UnsubscribeLink"><% _t('CLICKHERE','click here') %></a>.
					</td></tr>
				<% end_if %>
				<% if SiteConfig %>
					<tr><td align="left" class="footer">
						<a href="$BaseHref" style="">$SiteConfig.Title</a> $SiteConfig.Tagline
					</td></tr>
				<% end_if %>
			</tfoot>
	    </table> 
  
    </td></tr></table>
    </center>  
</body>
</html>