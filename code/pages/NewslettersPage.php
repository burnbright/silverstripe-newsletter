<?php

/**
 * Display newsletters on the front-end.
 */
class NewslettersPage extends Page{

	static $has_one = array(
		'NewsletterType' => 'NewsletterType'
	);


}

class NewslettersPage_Controller extends Page_Controller{

	static $url_segment = 'newsletters';

	function index(){
		if(!$this->Title){
			$this->Title = _t("NewslettersPage.NEWSLETTERs","Newsletters");
		}
		return array();
	}

	function getNewsletters(){
		$filter = "\"Status\" != 'Draft' AND \"ShowOnFront\" = 1";
		return DataObject::get('Newsletter',$filter);
	}

	/**
	 * Display an individual newsletter.
	 */
	function view(){
		$id = Director::urlParam('ID');
		if($newsletter = DataObject::get_by_id('Newsletter',$id)){
			if($newsletter->canView(Member::currentUser())){
				return array(
					'Title' => $newsletter->Subject,
					'Content' => $newsletter->obj('Content')->forTemplate(),
					'Newsletters' => false
				);
			}else{
				Security::permissionFailure($this,_t("Newsletter.LOGINTOVIEW"."You must be logged in to view this newsletter."));
				return;
			}
		}
		return $this->httpError(404);
	}

}