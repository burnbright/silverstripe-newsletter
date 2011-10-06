<?php

/**
 * Record to keep track of when a {@link Member} has
 * unsubscribed from a newsletter.
 *
 * @package newsletter
 */
class UnsubscribeRecord extends DataObject {

	static $has_one = array(
		'NewsletterType' => 'NewsletterType',
		//'Newsletter' => 'Newsletter', //keep a record of which newsletter caused unsubscription
		'Member' => 'Member'
	);

	/**
	 * Migrate data from Member_UnsubscribeRecord (the obsolete table)
	 * to UnsubscribeRecord.
	 */
	function requireDefaultRecords() {
		parent::requireDefaultRecords();

		if(in_array('Email_BlackList', DB::getConn()->tableList())) {
			DB::query("INSERT INTO \"UnsubscribeRecord\" SELECT * FROM \"Member_UnsubscribeRecord\"");
			DB::query("RENAME TABLE \"Member_UnsubscribeRecord\" TO \"_obsolete_Member_UnsubscribeRecord\"");

			echo("<div style=\"padding:5px; color:white; background-color:blue;\">Data in Member_UnsubscribeRecord has been moved to the new UnsubscribeRecord table. To drop the obsolete table, issue this SQL command: \"DROP TABLE '_obsolete_Member_UnsubscribeRecord'\".</div>");
		}
	}

	/**
	 * Unsubscribe the member from a specific newsletter type
	 *
	 * @param int|Member $member Member object or ID
	 * @param int|NewsletterType $newsletterType Newsletter type object or ID
	 */
	function unsubscribe($member, $newsletterType) {
		if(!class_exists('NewsletterType')) {
			user_error("UnsubscribeRecord::unsubscribe() called without the newsletter module available", E_USER_WARNING);
			return;
		}

		// $this->UnsubscribeDate()->setVal( 'now' );
		$this->MemberID = (is_numeric($member))
			? $member
			: $member->ID;

		$this->NewsletterTypeID = (is_numeric($newsletterType))
			? $newsletterType
			: $newsletterType->ID;

		$this->write();
	}
}