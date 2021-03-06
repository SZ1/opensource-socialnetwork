/**
 * 	OpenSource-SocialNetwork
 *
 * @package   (Informatikon.com).ossn
 * @author    OSSN Core Team <info@opensource-socialnetwork.com>
 * @copyright 2014 iNFORMATIKON TECHNOLOGIES
 * @license   General Public Licence http://opensource-socialnetwork.com/licence 
 * @link      http://www.opensource-socialnetwork.com/licence
 */
.ossn-profile {
 width:850px;
}
.ossn-profile .top-container{
 background:#fff;
 border: 1px solid #C4CDE0;
 border-width: 1px 1px 2px;
 height:360px;
 position: relative;
 z-index: 2;
}
.ossn-profile-sidebar {
 float: right;
 margin-top: -317px;
}
.ossn-profile .profile-photo {
 background-color: #fff;
 border: 1px solid #CCC;
 float: left;
 height: 170px;
 padding: 3px;
 margin-left: 20px;
 top: 160px;
 width: 170px;
 border-radius: 2px 2px 2px 2px;
 -webkit-border-radius: 2px 2px 2px 2px;
 -moz-border-radius: 2px 2px 2px 2px;
 
 position: absolute;
}
.ossn-profile .profile-cover {
 height:315px;
 background:#fff url('<?php echo ossn_site_url();?>themes/default/images/cover-bg.png') repeat-x;
}
.ossn-profile .profile-name {
 color: #FFF;
 font-weight: bold;
 margin-top: -60px;
 font-size: 0.6cm;
 margin-left: 211px;
 z-index: 1;
 position: relative;

}
.ossn-profile .profile-cover-img {
 
 }
.ossn-profile-bottom {
  width: 850px;
}
.ossn-profile-modules {
 display:inline-block;
 margin-top: 19px;
}
.ossn-profile-wall {
 display: inline-block;
 float: right;
 width: 528px;
 margin-top: 19px;
 height: 200px;
}
.upload-photo {
 background: #000;
 opacity: 0.5;
 width: 156px;
 padding: 7px;
 height: 20px;
 position: absolute;
 color: #FFF; 
 text-align: center;
 font-size: 15px;
 font-family: sans-serif;
}
.user-cover-uploading {
 opacity:0.4;
}
.user-photo-uploading {
 height:156px; 
 opacity:0.8;
 background:#fff;
 width: 156px;
 padding: 7px;
 position: absolute;
 color: #FFF; 
 text-align: center;
 font-size: 15px;
 font-family: sans-serif;
}
.profile-menu {
 float: right;
 position: relative;
 margin-top: 47px;
 margin-right: 20px;
}
.change-cover {
 float: right;
 position: relative;
 margin-right: 20px;
}
.reposition-cover {
 float: right;
 position: relative;
 margin-right: 5px;
}
.profile-cover-controls {
 position: absolute;
 width:850px;
 margin-top: 170px;
 z-index: 1
}

#cover-menu {
 display:none;
}
/**********
Wall
*********/
.comments-likes {
 background:#F6F7F8;
 min-height:50px;
 width:100%;
 border-top: 1px solid #E1E2E3;
}
.comments-likes .poster-image {
 padding: 10px;
 display:inline-table;
}
.comments-likes .comment-container {
 display:inline-table;
}
.comments-likes .comment-text {
 display: inline-table;
 vertical-align: top;
 margin-top: 10px;
 margin-left: -5px;
 font-size: 12px;
 width: 455px;
 margin-bottom: 6px;
}
.comments-likes .like_share {
padding: 10px;
border-bottom: 1px solid #EEE;
font-size: 12px;
}

.dot-comments {
 vertical-align: top;
 margin: -3px 2px 0 0px;
 display: inline-block;
}
.comments-likes textarea,
.comments-likes input[type='text'] {
float: right;
border: 1px solid #D3D6DB;
padding: 7px;
height: 16px;
margin-top: -32px;
margin-left: -7px;
width: 452px;
resize:none;
}
.comment-text p {
 margin-top: 1px;
 line-height: 15px;
}
.comment-metadata {
margin-top: -9px;
color:#9B9B9B;
}
.comments-item {
margin-bottom: -11px;
}

.comments-likes .like_share .button-container,
.comments-likes .like_share a{
 display:inline-block;
}


.ossn-profile-modules-about {
 padding:5px;
 font-size:12px;
}
.ossn-profile-modules-about-item .label{
	color:#6A7480;
	font-weight:bold;
	  display:inline-table;

}.ossn-profile-modules-about-item .metadata{
  color:#2B5470;
  font-weight:bold;
  display:inline-table;
}
.ossn-profile-modules-about-item {
 padding: 10px;
 border-bottom: 1px solid #eee;
}
.ossn-profile-modlue-friends img {
  padding: 1.5px;	
}
.ossn-profile-modlue-friends h3 {
 padding:4px;
 text-align:center;
 color:#ccc;
}  
/* newsfeed user details **/
.newseed-uinfo img {
 border: 1px solid rgba(0, 0, 0, 0.1);
 display:inline-table;
 height:40px;
 width:40px;
}

.newseed-uinfo .name {
 float:right;
 display:inline-table;
 font-weight: bold;
 width: 108px;
 margin-right: 5px;
}
.newseed-uinfo .name a{
 color:#141823;
 display:block;
 font-size: 12px;
 padding: 1px;
}
.newseed-uinfo .edit-profile {	
 font-weight:normal;
}

.newseed-uinfo .name a:hover{
text-decoration:underline;
}