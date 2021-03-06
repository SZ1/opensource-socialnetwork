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
define('__OSSN_PROFILE__', ossn_route()->com.'OssnProfile/');
require_once(__OSSN_PROFILE__.'classes/OssnProfile.php');

function ossn_profile(){
  //pages
  ossn_register_page('u', 'profile_page_handler');
  ossn_register_page('avatar', 'avatar_page_handler');
  ossn_register_page('cover', 'cover_page_handler');
  //css and js
  ossn_extend_view('css/ossn.default', 'components/OssnProfile/css/profile');
  ossn_extend_view('js/opensource.socialnetwork', 'components/OssnProfile/js/OssnProfile'); 
  //actions
  ossn_register_action('profile/photo/upload', __OSSN_PROFILE__.'actions/photo/upload.php');
  ossn_register_action('profile/cover/upload', __OSSN_PROFILE__.'actions/cover/upload.php');
  ossn_register_action('profile/cover/reposition', __OSSN_PROFILE__.'actions/cover/reposition.php');
  ossn_register_action('profile/edit', __OSSN_PROFILE__.'actions/edit.php');
  //callback
  ossn_register_callback('page', 'load:search', 'ossn_search_users_link');
}
function ossn_search_users_link($event, $type, $params){
	$url = OssnPagination::constructUrlArgs();
	ossn_register_menu_link('search:users', 'search:users', "search?type=users{$url}", 'search');	
}
if(ossn_isLoggedin()){
 $user_loggedin = ossn_loggedin_user();
 $icon = ossn_site_url('components/OssnProfile/images/friends.png');
 ossn_register_sections_menu('newsfeed', array(
								   'text' => ossn_print('user:friends'), 
								   'url' => "{$url}u/{$user_loggedin->username}/friends", 
								   'section' => 'links',
								   'icon' => $icon
								   ));	

}

ossn_add_hook('newsfeed', "left", 'profile_photo_newsefeed', 1);
ossn_add_hook('profile', 'subpage', 'profile_user_friends');
ossn_add_hook('search', 'type:users', 'profile_search_handler');
ossn_add_hook('profile', 'subpage', 'profile_edit_page');

ossn_add_hook('profile', 'modules', 'profile_modules');
ossn_register_callback('page', 'load:profile', 'ossn_profile_load_event');

function ossn_profile_load_event($event, $type, $params){
	$owner = ossn_user_by_guid(ossn_get_page_owner_guid());
	$url = ossn_site_url();
	ossn_register_menu_link('timeline', 'timeline', "{$url}u/{$owner->username}", 'user_timeline');
	ossn_register_menu_link('friends', 'friends', "{$url}u/{$owner->username}/friends", 'user_timeline');
}
function ossn_profile_subpage($page){
   global $VIEW;
   return $VIEW->pagePush[] = $page;
}
function ossn_is_profile_subapge($page){
 global $VIEW;
 if(in_array($page, $VIEW->pagePush)){
	 return true; 
 }
return false; 
}
ossn_profile_subpage('friends');
function profile_user_friends($hook, $type, $return, $params){
  $page = $params['subpage'];
  if($page == 'friends'){
	$user['user'] = $params['user'];  
	$friends = ossn_view('components/OssnProfile/pages/friends', $user);  
	echo ossn_set_page_layout('module', array(
											'title' => ossn_print('friends'),
											'content' => $friends,
											));
  }
}

ossn_profile_subpage('edit');
function profile_edit_page($hook, $type, $return, $params){
  $page = $params['subpage'];
  if($page == 'edit'){
	$user['user'] = $params['user'];  
	if($user['user']->guid !== ossn_loggedin_user()->guid){
	  redirect(REF);	
	}
    $params = array(
					 'action' => ossn_site_url().'action/profile/edit',
					 'component' => 'OssnProfile',
					 'class' => 'ossn-edit-form',
					 'params' => $user,
						);
    $form = ossn_view_form('edit', $params , false);	
	echo ossn_set_page_layout('module', array(
											'title' => ossn_print('edit'),
											'content' => $form,
											));
  }
}
function profile_search_handler($hook, $type, $return, $params){
	$Pagination = new OssnPagination;
	$users = new OssnUser;
	$data = $users->searchUsers($params['q']);
    $Pagination->setItem($data);   
    $user['users'] = $Pagination->getItem();
    $search = ossn_view('system/templates/users', $user);
    $search .= $Pagination->pagination();	
	if(empty($data)){
	  return 'No result found';	
	}
	return $search;
}
function profile_modules($h, $t, $module, $params){
	$user['user'] = $params['user'];
	
	// didn't part of initial release , so in next release we will add
	/*$content = ossn_view("components/OssnProfile/modules/about", $user);
	$modules[] = ossn_view_widget('profile/widget', 'ABOUT', $content);*/
	
	$content = ossn_view("components/OssnProfile/modules/friends", $user);
	$modules[] = ossn_view_widget('profile/widget', 'FRIENDS', $content);
	
	return $modules;
}
function profile_photo_newsefeed($hook, $type, $return){
   $return[] = ossn_view('components/OssnProfile/newsfeed/info');
   return $return;	
}
function profile_page_handler($page){
	  $user = ossn_user_by_username($page[0]);
	  if(empty($user->guid)){
		 ossn_error_page();  
	  }
	  ossn_set_page_owner_guid($user->guid);
	  ossn_trigger_callback('page', 'load:profile');
	  
	  $params['user'] = $user;
	  $params['page'] = $page;
	  if(isset($page[1])){
	   $params['subpage'] =  $page[1];
	  } else { $params['subpage']  = ''; }
	  if(!ossn_is_profile_subapge($params['subpage']) && !empty($params['subpage'])){
		 return false;  
	  }
	  $title = $user->fullname;
	  $contents['content'] = ossn_view('components/OssnProfile/pages/profile', $params);
      $content = ossn_set_page_layout('contents', $contents);
      echo ossn_view_page($title, $content);    
}
function get_profile_photo_guid($guid){
	 $photo = new OssnFile;
     $photo->owner_guid = $guid;
	 $photo->type = 'user';
	 $photo->subtype = 'profile:photo';
	 $photos = $photo->getFiles();
	 if(isset($photos->{0}->guid)){
		 return $photos->{0}->guid; 
	 }
	return false; 
}
function get_profile_photo($guid , $size){
     $photo = new OssnFile;
     $photo->owner_guid = $guid;
	 if(isset($size) && array_key_exists($size, ossn_user_image_sizes())){
		 $isize = "{$size}_";
	 } 
     $photo->type = 'user';
	 $photo->subtype = 'profile:photo';
	 $photos = $photo->getFiles();
	 if(isset($photos->{0}->value) && !empty($photos->{0}->value)){
		   $datadir = ossn_get_userdata("user/{$guid}/{$photos->{0}->value}"); 
		 if(!empty($size)){
		    $image = str_replace('profile/photo/', '', $photos->{0}->value); 
		    $datadir = ossn_get_userdata("user/{$guid}/profile/photo/{$isize}{$image}");
		 } 
		 return file_get_contents($datadir);
	 } else {
			$datadir = ossn_default_theme()."images/nopictures/users/{$size}.jpg";  
			return file_get_contents($datadir);
	 }
return false;	 
}

function get_cover_photo($guid){
     $photo = new OssnFile;
     $photo->owner_guid = $guid;
     $photo->type = 'user';
	 $photo->subtype = 'profile:cover';
	 $photos = $photo->getFiles();
	 if(isset($photos->{0}->value)){
		  $datadir = ossn_get_userdata("user/{$guid}/{$photos->{0}->value}"); 
		 return file_get_contents($datadir);
	 }
return false;	 
}
function cover_page_handler($avatar){
	if(isset($avatar[0])){
	   $user = ossn_user_by_username($avatar[0]);	
		if(!empty($user->guid)){
	       header('Content-Type: image/jpeg');
		   echo get_cover_photo($user->guid);
		}
	}
}
function avatar_page_handler($avatar){
	if(isset($avatar[0])){
	     if(!isset($avatar[1]) && empty($avatar[1])){
			$avatar[1] = ''; 
		 }
	    $user = ossn_user_by_username($avatar[0]);	
		if(!empty($user->guid)){
	        header('Content-Type: image/jpeg');
		   echo get_profile_photo($user->guid, $avatar[1]);
		} else {
		ossn_error_page();	
		}
	}
}
ossn_register_callback('ossn', 'init', 'ossn_profile');
