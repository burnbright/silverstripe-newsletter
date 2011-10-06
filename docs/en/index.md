Newsletters
===========

This newsletter module documentation is for website developers and designers.

## Installation

 * Setting up email bounce handling 

 
## Templates

It can be difficult to create a html email template that works properly in most email clients.
Here is some help and resources to get you on your way.

Start by looking at the GenericNewsletter.ss template, found in /newsletter/email/.

All css styles for emails should be inline. Therefore you should either convert all the email content to use in-line styles,
or you should have them converted automatically.

 * http://htmlemailboilerplate.com - create an email template that actually works.
 * http://www.campaignmonitor.com/templates/ - free templates from campaign monitor
 * https://github.com/mailchimp/Email-Blueprints - email blueprints courtesy of mailchimp
 
## How open and click tracking works
 
When a newsletter is sent, the individual links are replaced with alternative links.
A 1x1 px gif image is also included in the email body.
 
 ...