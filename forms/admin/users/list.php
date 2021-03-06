<?php
/**
 * 	OpenSource-SocialNetwork
 *
 * @package   (Informatikon.com).ossn
 * @author    OSSN Core Team <info@opensource-socialnetwork.com>
 * @copyright 2014 iNFORMATIKON TECHNOLOGIES
 * @license   General Public Licence http://opensource-socialnetwork.com/licence 
 * @link      http://www.opensource-socialnetwork.com/licence
 */
$search = input('search_users');
$users = new OssnUser;
$pagination = new OssnPagination;

if(!empty($search)){
    $pagination->setItem($users->SearchSiteUsers($search));
} else {
    $pagination->setItem($users->getSiteUsers());
}
?>
<div class="top-controls">
<a href="<?php echo ossn_site_url("administrator/adduser");?>" class="ossn-admin-button button-green"><?php echo ossn_print('add');?></a>
<input type="submit" class="ossn-admin-button button-red" value="Delete" />
</div>
<table class="table ossn-users-list">  
<tbody>
  <tr class="table-titles">
    <td> </td>
    <td><?php echo ossn_print('name');?></td>
    <td><?php echo ossn_print('username');?></td>
    <td><?php echo ossn_print('email');?></td>
    <td><?php echo ossn_print('type');?></td>
    <td><?php echo ossn_print('lastlogin');?></td>
    <td><?php echo ossn_print('edit');?></td>
  </tr>
  <?php foreach($pagination->getItem() as $user){
	  $user = ossn_user_by_guid($user->guid);
	  ?>
  <tr>
    <td><input type="checkbox" /></td>
    <td>
    <div class="image"><img src="<?php echo ossn_site_url();?>avatar/<?php echo $user->username;?>/smaller" /></div>
	<div class="name" style="margin-left:39px;margin-top: -39px;"><?php echo sttl($user->fullname, 20);?></div></td>
    <td><?php echo $user->username;?></td>
    <td><?php echo $user->email;?></td>
    <td><?php echo $user->type;?></td>
    <td><?php echo $user->type;?></td>
    <td><a href="<?php echo ossn_site_url("administrator/edituser/{$user->username}");?>"><?php echo ossn_print('edit');?></a></td>

  </tr>
  <?php } ?>
  </tbody>
</table>
<?php echo $pagination->pagination();?>
