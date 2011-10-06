<?php

/**
 * {@link Newsletter} objects have their links rewritten to use tracking hashs
 * so when a user receives an email links will be in the form newsletterlinks/track/8sad8903458sa
 * which is a link to this controller.
 *
 * This controller then determines the correct location for that hashcode and redirects
 * the user to the webpage
 *
 * @package newsletter
 */

class EmailTrackingController extends ContentController {

	static $url_segment = "newsletterlinks";

	function init() {
		parent::init();
		//TODO: introduce backwards-compatability for old link styles
		//if $Action != 'link' or 'open'', then transfer it to 'link' for backwards compatibility
	}

	function index(){
		return $this->track();
	}

	/**
	 * Records a link click.
	 */
	function track(){

		if($params = $this->getURLParams()) {

			//different link versions, for maintaining backwards compatability
			if(isset($params['Version']) && $params['Version'] == "v2"){

				if($decrypted = $this->decryptHash()){

					if($recipient = DataObject::get_one("Newsletter_SentRecipient","\"Email\" = '".$decrypted['e']."' AND \"ParentID\" = ".$decrypted['nl'])){
						$recipient->recordClick();
					}
					if(isset($decrypted['l']) && is_numeric($decrypted['l']) && $link = DataObject::get_by_id('Newsletter_TrackedLink', (int)$decrypted['l'])){
						$link->recordClick();
						return $this->redirect($link->Original, 301);
					}
				}

			}elseif(isset($params['Hash']) && ($hash = Convert::raw2sql($params['Hash']))) {
				$link = DataObject::get_one('Newsletter_TrackedLink', "\"Hash\" = '$hash'");
				if($link) {
					// check for them visiting this link before
					$link->recordClick();
					return $this->redirect($link->Original, 301);
				}
			}

		}
		return $this->httpError(404);
	}

	/**
	 * Records an email open with a web bug image.
	 * Returns a 1px by 1px gif image.
	 *
	 * eg link: http://mysite/newsletterlinks/open/asdfhasdfaslh23hzlaslk34h34lhgsl
	 */
	function open(){

		$expires = gmdate('D, d M Y H:i:s \G\M\T', strtotime("+1 year"));
		if($decrypted = $this->decryptHash()){
			if(	$recipient = DataObject::get_one("Newsletter_SentRecipient","\"Email\" = '".$decrypted['e']."' AND \"ParentID\" = ".$decrypted['nl'])){
				$recipient->recordOpen();
				$expires = gmdate('D, d M Y H:i:s \G\M\T', time()); //set expires to present so that we can record future opens
			}
		}

		header("content-type: image/gif");
		header("Expires: $expires"); //set long expiry...or the period to wait until an open of the same email is relevant
		//this outputs the bits for a 1x1 px gif image
		echo chr(71).chr(73).chr(70).chr(56).chr(57).chr(97).
		      chr(1).chr(0).chr(1).chr(0).chr(128).chr(0).
		      chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).
		      chr(33).chr(249).chr(4).chr(1).chr(0).chr(0).
		      chr(0).chr(0).chr(44).chr(0).chr(0).chr(0).chr(0).
		      chr(1).chr(0).chr(1).chr(0).chr(0).chr(2).chr(2).
		      chr(68).chr(1).chr(0).chr(59);
		die(); //halt exection, and don't sent SS headers
	}

	static function generate_webbug_url($recipient = null){
		$id = ($recipient) ? $recipient->ID: "";
		return Director::absoluteURL(self::$url_segment."/open/$id",true);
	}

	static function generate_link_url($linkhash,$recipient = null){
		$id = ($recipient) ? $recipient->ID: "";
		return Director::absoluteURL(self::$url_segment."/track/".$linkhash."/".$id,true);
	}

	static function generate_hash($newsletter,$email,$link = null){
		$data = array(
			"nl" => $newsletter->ID,
			"e" => $email
		);
		if($link) $data['l'] = $link;
		$encoded = urlencode(serialize($data)); //make it possible to store in a url
		//TODO: introduce mcrypt to prevent tampering
		return 	$encoded;
	}

	protected function decryptHash(){
		$hash = Director::urlParam("Hash");
		if(!$hash && isset($_GET['h'])) $hash = $_GET['h'];

		$arr = unserialize(urldecode($hash));
		if(is_array($arr) && isset($arr['nl']) && is_numeric($arr['nl']) && isset($arr['e']) && $arr['e'] != ""){
			return $arr;
		}
		return null;
	}

}