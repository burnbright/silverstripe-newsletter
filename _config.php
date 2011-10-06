<?php
/**
 * URL rules for the newsletter module
 * 
 * @package newsletter
 */

define('NEWSLETTER_DIR', 'newsletter');
Director::addRules(50, array(
	EmailTrackingController::$url_segment.'/$Action/$Hash/$Version' => "EmailTrackingController",
	EmailTrackingController::$url_segment.'/open.gif' => "EmailTrackingController/open", //allow for more authentic looking image url
	UnsubscribeController::$url_segment.'//$Action/$AutoLoginHash/$MailingList' => 'UnsubscribeController',
	NewslettersPage_Controller::$url_segment.'/$Action/$ID' => 'NewslettersPage_Controller'
));

Object::add_extension('NewsletterEmail', 'TrackingLinksEmail');
DataObject::add_extension('Member', 'NewsletterRole');