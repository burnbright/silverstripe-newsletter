<?php

/**
 * Single newsletter instance.  Each {@link Newsletter} belongs to a {@link NewsletterType}.
 * @package newsletter
 */
class Newsletter extends DataObject {

	static $db = array(
		"Status" => "Enum('Draft, Send', 'Draft')",
		"Content" => "HTMLText",
		"Subject" => "Varchar(255)",
		"SentDate" => "Datetime"
	);

	static $has_one = array(
		"Parent" => "NewsletterType",
	);

	static $has_many = array(
		"SentRecipients" => "Newsletter_SentRecipient",
		"TrackedLinks" => "Newsletter_TrackedLink"
	);

	/**
	 * Returns a FieldSet with which to create the CMS editing form.
	 * You can use the extend() method of FieldSet to create customised forms for your other
	 * data objects.
	 *
	 * @param Controller
	 * @return FieldSet
	 */
	function getCMSFields($controller = null) {
		$group = DataObject::get_by_id("Group", $this->Parent()->GroupID);
		$sentReport = $this->renderWith("Newsletter_StatusReport");
		$previewLink = Director::absoluteBaseURL() . 'admin/newsletter/preview/' . $this->ID;
		$trackedLinks = $this->renderWith("Newsletter_TrackedLinksReport");

		$ret = new FieldSet(
			$roottabset = new TabSet("Root",
				$mailTab = new Tab(_t('Newsletter.CONTENT', 'Content'),
					new TextField("Subject", _t('Newsletter.SUBJECT', 'Subject'), $this->Subject),
					new HtmlEditorField("Content", _t('Newsletter.CONTENT', 'Content')),
					new LiteralField('PreviewNewsletter', "<a href=\"$previewLink\" target=\"_blank\" class=\"action\">" . _t('EMAILPREVIEW', 'Email Preview') . "</a>"),
					new LiteralField('FrontEndView',"<a target=\"blank\" href=\"".$this->Link()."\">"._t('PAGEPREVIEW','Page Preview')."</a>")
				)
			)
		);

		if($this->Status != 'Draft') {
			$roottabset->push($sentToTab = new Tab(_t('Newsletter.STATUSREPORT', 'Status Report'),
				new LiteralField("SentStatusReport", $sentReport)
			));

			$roottabset->push($trackTab = new Tab(_t('Newsletter.ANALYTICS', 'Analytics'),
				new LiteralField("TrackedLinks", $trackedLinks)
			));

			$mailTab->insertAfter( new ReadonlyField("SentDate", _t('Newsletter.SENTAT', 'Sent at'), $this->SentDate),'Subject');

		}

		$this->extend("updateCMSFields", $ret);
		return $ret;
	}

	/**
	 * Only let recipients view newsletters by default.
	 */
	function canView($member = null){
		if(Permission::check('ADMIN')) return true;
		return($member && $member->inGroup($this->Parent()->GroupID));
	}

	/**
	 * Returns a DataObject listing the recipients for the given status for this newsletter
	 *
	 * @param string $result 3 possible values: "Sent", (mail() returned TRUE), "Failed" (mail() returned FALSE), or "Bounced" ({@see $email_bouncehandler}).
	 * @return DataObjectSet
	 */
	function Recipients($result,$extrafilter = null) {
		$SQL_result = Convert::raw2sql($result);
		$filter = array("ParentID='".$this->ID."'", "Result='".$SQL_result."'");
		if($extrafilter) $filter[] = $extrafilter;
		return DataObject::get("Newsletter_SentRecipient",$filter);
	}

	/**
	 * Returns a DataObjectSet containing the subscribers who have never been sent this Newsletter
	 *
	 * @return DataObjectSet
	 */
	function UnsentSubscribers() {
		// Get a list of everyone who has been sent this newsletter
		$sent_recipients = DataObject::get("Newsletter_SentRecipient","ParentID='".$this->ID."'");
		// If this Newsletter has not been sent to anyone yet, $sent_recipients will be null
		if ($sent_recipients != null) {
			$sent_recipients_array = $sent_recipients->toNestedArray('MemberID');
		} else {
			$sent_recipients_array = array();
		}

		// Get a list of all the subscribers to this newsletter
        if(defined('DB::USE_ANSI_SQL')) {
			$subscribers = DataObject::get( 'Member', "\"GroupID\"='".$this->Parent()->GroupID."'", null, "INNER JOIN \"Group_Members\" ON \"MemberID\"=\"Member\".\"ID\"" );
        } else {
        	$subscribers = DataObject::get( 'Member', "`GroupID`='".$this->Parent()->GroupID."'", null, "INNER JOIN `Group_Members` ON `MemberID`=`Member`.`ID`" );
        }
		// If this Newsletter has no subscribers, $subscribers will be null
		if ($subscribers != null) {
			$subscribers_array = $subscribers->toNestedArray();
		} else {
			$subscribers_array = array();
		}

		// Get list of subscribers who have not been sent this newsletter:
		$unsent_subscribers_array = array_diff_key($subscribers_array, $sent_recipients_array);

		// Create new data object set containing the subscribers who have not been sent this newsletter:
		$unsent_subscribers = new DataObjectSet();
		foreach($unsent_subscribers_array as $key => $data) {
			$unsent_subscribers->push(new ArrayData($data));
		}

		return $unsent_subscribers;
	}

	function getTitle() {
		return $this->getField('Subject');
	}

	function getNewsletterType() {
		return DataObject::get_by_id('NewsletterType', $this->ParentID);
	}

	function getContentBody(){
		$content = $this->obj('Content');

		$this->extend("updateContentBody", $content);
		return $content;
	}

	static function newDraft($parentID, $subject, $content) {
    	if( is_numeric($parentID)) {
     	   $newsletter = new Newsletter();
	        $newsletter->Status = 'Draft';
	        $newsletter->Title = $newsletter->Subject = $subject;
	        $newsletter->ParentID = $parentID;
	        $newsletter->Content = $content;
	        $newsletter->write();
	    } else {
	        user_error( $parentID, E_USER_ERROR );
	    }
    	return $newsletter;
  	}

	function PreviewLink(){
		return Controller::curr()->AbsoluteLink()."preview/".$this->ID;
	}

	function Link(){
		if($np = DataObject::get_one('NewslettersPage')){
			return $np->Link('view')."/".$this->ID;
		}
		return NewslettersPage_Controller::$url_segment."/view/".$this->ID;
	}

	/**
	 * Returns a list of all the {@link Newsletter_TrackedLink} objects attached
	 * to this newsletter and sorts them in desc order
	 *
	 * @return DataObjectSet|false
	 */
	function NewsletterLinks() {
		$links = $this->TrackedLinks();
 		if($links) {
			$links->sort("\"Visits\"", "DESC");
			return $links;
		}
	}

	function Stats(){

		if($this->Status == 'Draft')
			return array();

		$sent = $opened = $unopened = $bounced = $notsent = $unsubs = $clicks = 0;
		$opened_p = $unopened_p = $bounced_p = 0;
		if($recipients = $this->Recipients('Sent'))
			$sent = $recipients->Count();
		if($trackedlinks = $this->TrackedLinks()){
			foreach($trackedlinks as $link){
				$clicks += $link->Visits;
			}
		}
		if($or = $this->Recipients('Sent','"FirstOpened" IS NOT NULL'))
			$opened = $or->Count();

		//TODO: bounced

		$unopened = $sent - $opened;

		$opened_p = percent($opened,$sent);
		$unopened_p = percent($unopened,$sent);
		$bounced_p = percent($bounced,$sent);
		$notsent = $this->UnsentSubscribers()->Count();

		$success_rate = percent(($opened - $bounced),$sent);



		return new ArrayData(array(
			'Sent' => $sent,
			'Opened' => $opened,
			'Unopened' => $unopened,
			'Bounced' => $bounced,
			'NotSent' => $notsent,
			'Unsubscribes' => $unsubs,
			'Clicks' => $clicks,

			'OpenedPercent' => $opened_p,
			'UnopenedPercent' => $unopened_p,
			'BouncedPercent' => $bounced_p,

			'SuccessRate' => $success_rate
			//clicks percent of total, clicks percent of opened
		));
	}



}

/**
 * Database record for recipients that have had the newsletter sent to them.
 *
 * @package newsletter
 */
class Newsletter_SentRecipient extends DataObject {
	/**
	 *	Result has 4 possible values: "Sent", (mail() returned TRUE), "Failed" (mail() returned FALSE),
	 * 	"Bounced" ({@see $email_bouncehandler}), or "BlackListed" (sending to is disabled).
	 */
	static $db = array(
		"Email" => "Varchar(255)",
		"Result" => "Enum('Sent,Bounced,Failed,BlackListed', 'Sent')",

		"Opens" => "Int",
		"Clicks" => "Int",

		"FirstOpened" => "Datetime",
		"LastOpened" => "Datetime",

		"IP" => "Varchar(50)",
		"Agent" => "Varchar(255)"
	);
	static $has_one = array(
		"Member" => "Member",
		"Parent" => "Newsletter"
	);

	/**
	 * Record that an email has been opened.
	 */
	function recordOpen(){
		$cont = Controller::curr();
		$request = $cont->getRequest();
		$this->Opens ++;
		if(!$this->FirstOpened) $this->FirstOpened = time();
		$this->LastOpened = time();
		$this->IP = $request->getIP(); //ip address = can find out country
		$this->Agent = $_SERVER['HTTP_USER_AGENT'];
		$this->write();
	}

	/**
	 *  record link clicks for each recipient
	 */
	function recordClick(){
		if(!$this->FirstOpened) $this->recordOpen();
		//TODO: also record an open if last open was greater than say 1 hour ago?
		$this->Clicks ++;
		$this->write();
	}

	function Status(){
		if($this->Clicks) return "Actioned";
		if($this->Opens) return "Opened";
		return $this->Result;
	}

}

/**
 * Tracked link is a record of a link from the {@link Newsletter}
 *
 * @package newsletter
 */
class Newsletter_TrackedLink extends DataObject {

	static $db = array(
		'Original' => 'Varchar(255)',
		'Hash' => 'Varchar(100)',
		'Visits' => 'Int'
	);

	static $has_one = array(
		'Newsletter' => 'Newsletter'
	);

	/**
	 * Generate a unique hash
	 * @deprecated the TrackedLink ID is now used instead.
	 */
	function onBeforeWrite() {
		parent::onBeforeWrite();
		if(!$this->Hash) $this->Hash = md5(time() + rand());
	}

	/**
	 * Return the full link to the hashed url, not the
	 * actual link location
	 *
	 * @deprecated use EmailTrackingController::generate_link_url() instead
	 * @return String
	 */
	function Link() {
		if(!$this->Hash) $this->write();
		return EmailTrackingController::generate_link_url($this->Hash);
	}

	/**
	 * Record an individual link click.
	 */
	function recordClick(){
		if(!Cookie::get('ss-newsletter-link-'.$this->ID)) {
			$this->Visits++;
			$this->write();
			Cookie::set('ss-newsletter-link-'. $this->ID, true);
		}
	}
}

function percent($num_amount, $num_total) {
	$count1 = ($num_total) ? $num_amount / $num_total : 0;
	$count2 = $count1 * 100;
	return number_format($count2, 0)."%";
}