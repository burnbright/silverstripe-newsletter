<?php

/**
 * Form field showing a list of bounced addresses
 *
 * @package newsletter
 */
class BouncedList extends FormField {

    protected $nlType;

    function __construct( $name, $newsletterType ) {
		parent::__construct( $name, '', null );

        if( is_object( $newsletterType ) )
            $this->nlType = $newsletterType;
        else
            $this->nlType = DataObject::get_by_id( 'NewsletterType', $newsletterType );
    }

    function setController($controller) {
		$this->controller = $controller;
	}

	function FieldHolder() {
		return $this->renderWith( 'NewsletterAdmin_BouncedList' );
	}

    function Entries() {

        $id = $this->nlType->GroupID;

        if(defined('DB::USE_ANSI_SQL')) {
        	//$bounceRecords = DataObject::get( 'Email_BounceRecord', "\"GroupID\"=\"$id\"", null, "INNER JOIN \"Group_Members\" USING(\"MemberID\")" );
        	$bounceRecords = DataObject::get( 'Email_BounceRecord', "\"GroupID\"='$id'", null, "INNER JOIN \"Group_Members\" ON \"Email_BounceRecord\".\"MemberID\" = \"Group_Members\".\"MemberID\"" );
        } else {
        	$bounceRecords = DataObject::get( 'Email_BounceRecord', "`GroupID`='$id'", null, "INNER JOIN `Group_Members` USING(`MemberID`)" );
        }

        //user_error($id, E_USER_ERROR );

        if( !$bounceRecords )
            return null;

        foreach( $bounceRecords as $bounceRecord ) {
			if( $bounceRecord ) {
				$bouncedUsers[] = new ArrayData( array(
                    'Record' => $bounceRecord,
                    'GroupID' => $id,
                    'Member' => DataObject::get_by_id( 'Member', $bounceRecord->MemberID )
                ));
            }
        }

        return new DataObjectSet( $bouncedUsers );
    }
}
?>