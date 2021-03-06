<?php
/**
 * OpenSocialWebsite
 *
 * @package   OpenSocialWebsite
 * @author    Open Social Website Core Team <info@opensocialwebsite.com>
 * @copyright 2014 iNFORMATIKON TECHNOLOGIES
 * @license   General Public Licence http://www.opensocialwebsite.com/licence 
 * @link      http://www.opensocialwebsite.com/licence
 */
define('__OSSN_ADS__', ossn_route()->com.'OssnAds/');
require_once(__OSSN_ADS__.'classes/OssnAds.php');

function ossn_ads(){  
  ossn_register_com_panel('OssnAds', 'settings');
  ossn_register_action('ossnads/add', __OSSN_ADS__.'actions/add.php');
  ossn_register_page('ossnads', 'ossn_ads_handler');
  ossn_extend_view('css/ossn.default', 'components/OssnAds/css/ads');	
}
function ossn_ad_image($guid){
	 $photo = new OssnFile;
     $photo->owner_guid = $guid;
     $photo->type = 'object';
	 $photo->subtype = 'ossnads';
	 $photos = $photo->getFiles();
	 if(isset($photos->{0}->value) && !empty($photos->{0}->value)){
	    $datadir = ossn_get_userdata("object/{$guid}/{$photos->{0}->value}"); 
		return file_get_contents($datadir);
	 }
}
function ossn_ads_handler($pages){
  	$page = $pages[0];
    if(empty($page)){
		return false;
	}
	switch($page){
    case 'photo':
	header('Content-Type: image/jpeg');	
	if(!empty($pages[1]) 
					 && !empty($pages[1]) 
					 && $pages[2] == md5($pages[1]).'.jpg'){
	  echo ossn_ad_image($pages[1]);
	}
	break;	
	default:
         echo ossn_error_page();
    break;		
	}
}
function ossn_ads_image_url($guid){
  $image = md5($guid);
  return ossn_site_url("ossnads/photo/{$guid}/{$image}.jpg");	
}
ossn_register_callback('ossn', 'init', 'ossn_ads');
