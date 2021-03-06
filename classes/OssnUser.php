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

class OssnUser extends OssnEntities{
   /**
	* Initialize the objects.
	*
	* @return void;
	*/		
	public function initAttributes(){
		$this->OssnDatabase = new OssnDatabase;
		$this->notify = new OssnMail;
		if(!isset($this->sendactiviation)){
		  $this->sendactiviation = false;	
		}
	}
	/**
	* Generate salt.
	*
	* @return string;
	*/		
	public function generateSalt(){
        return substr(uniqid(), 5);	
    } 
	/**
	* Generate password.
	*
	* @return hash;
	*/		
    public function generate_password($password, $salt){
	    return md5($password.$salt);
    }
	/**
	* Check if the user is correct or not.
	*
	* @return bool;
	*/		
    public function isUsername(){
         if(preg_match("/^[a-zA-Z0-9]+$/", $this->username) && strlen($this->username) > 4){
	         return true;  
         }
     return false; 
    }
	/**
	* Check if password is > 5 or not.
	*
	* @return bool;
	*/		
    public function isPassword(){
       if(strlen($this->password) > 5){
	       return true;   
	   }
	   return false; 
    }
	/**
	* Get user with its entities.
	*
	* @return object;
	*/		
	public function getUser(){
		self::initAttributes();
		if(!empty($this->email)){
			$params['from'] = 'ossn_users';
			$params['wheres'] = array("email='{$this->email}'");
    	    $user = $this->OssnDatabase->select($params); 
			
	     }elseif(!empty($this->username)){
			$params['from'] = 'ossn_users';
			$params['wheres'] = array("username='{$this->username}'");
    	    $user = $this->OssnDatabase->select($params); 
		} elseif(!empty($this->guid)){
			$params['from'] = 'ossn_users';
			$params['wheres'] = array("guid='{$this->guid}'");
    	    $user = $this->OssnDatabase->select($params); 	
		}
	    $user->fullname = "{$user->first_name} {$user->last_name}";
		$this->owner_guid = $user->guid;
		$this->type = 'user';
		$entities = $this->get_entities();
		if(empty($entities)){
		   return arrayObject($user, get_class($this));
		}
		foreach($entities as $entity){
		    $fields[$entity->subtype] = $entity->value;
		}
		$data = array_merge(get_object_vars($user), $fields);
		return arrayObject($data, get_class($this));
	}
	/**
	* Add user to system.
	*
	* @return bool;
	*/		
	public function addUser(){
		self::initAttributes();
		if(empty($this->usertype)){
		  $this->usertype = 'normal';	
		}
	    $user = $this->getUser();
		if(empty($user->username) && $this->isPassword() && $this->isUsername()){
	        $this->salt = $this->generateSalt();
            $password = $this->generate_password($this->password, $this->salt);
	        $activation = md5($this->password.time().rand());
            if($this->sendactiviation == false){
			  $activation = NULL;	
			}
   		    $params['into'] = 'ossn_users';
			$params['names'] = array(
									 'first_name', 'last_name', 'email', 'username',
									 'type', 'password', 'salt', 'activation'
									 );
			$params['values'] = array(
									  $this->first_name, $this->last_name, $this->email, $this->username,
									  $this->usertype, $password, $this->salt, $activation
									  );
			if($this->OssnDatabase->insert($params)){
			  	 $guid = $this->OssnDatabase->getLastEntry();
				 if(!empty($guid) && is_int($guid)){
					$this->owner_guid = $guid;
					$this->type = 'user';
					
					$this->subtype = 'gender';
					$this->value = $this->gender;
					$this->add();
					
				    $this->subtype = 'birthdate';
					$this->value = $this->birthdate;
					$this->add();		
				 }
				  if($this->sendactiviation == true){
					$link = ossn_site_url("uservalidate/acitvate/{$guid}/{$activation}");
					$sitename = ossn_site_settings('site_name');
					$activation = ossn_print('ossn:add:user:mai:body', array($sitename, $link, ossn_site_url()));
					$subject = ossn_print('ossn:add:user:mail:subject', array($this->first_name, $sitename));
	                $this->notify->NotifiyUser($this->email, $subject, $activation);
				  }
				 return true;
			}
		}
       return false;	 	
	}
	/**
	* Login into site.
	*
	* @return bool;
	*/		
	public function Login(){
		$user = $this->getUser();
		$salt = $user->salt;
        $password = $this->generate_password($this->password.$salt);
        if($password == $user->password && $user->activation == NULL){
			unset($user->password);
			unset($user->salt);
	        $_SESSION['OSSN_USER'] = $user;
			$this->update_last_login();
			return true;
		}
		return false;
	}
	/**
	* Check if the user is friend with other or not.
	*
	* @return bool;
	*/		
	public function isFriend($usera, $user2){
      	$this->statement("SELECT * FROM ossn_relationships WHERE(
					     relation_from='{$usera}' AND 
					     relation_to='{$user2}' AND
					     type='friend:request'
					     );");
	    $this->execute();
	    $from = $this->fetch();
	    $this->statement("SELECT * FROM ossn_relationships WHERE(
					     relation_from='{$user2}' AND 
					     relation_to='{$usera}' AND
				 	     type='friend:request'
					     );");
	    $this->execute();
	    $to = $this->fetch();
	    if(isset($from->relation_id) && isset($to->relation_id)){
	    return true;	
     	}
	    return false;	
	}
   /**
	* Get user friends requests.
	*
	* @return object;
	*/		
    public function getFriendRequests($user){
		if(isset($this->guid)){
		 $user = $this->guid;	
		}
	    $this->statement("SELECT * FROM ossn_relationships WHERE(
					     relation_to='{$user}' AND
					     type='friend:request'
					     );");
	    $this->execute();
	    $from = $this->fetch(true);
        if(!is_object($from)){
	   	   return false; 
	    }
	    foreach($from  as $fr){
            if(!$this->isFriend($user,  $fr->relation_from)){
                $uss[] =  ossn_user_by_guid($fr->relation_from);
            }
		}
	   if(isset($uss)){
		   return $uss; 
	   }
	   return false;
	}
   /**
	* Get user friends.
	*
	* @return object;
	*/		
    public function getFriends($user){
		if(isset($this->guid)){
		 $user = $this->guid;	
		}
	    $this->statement("SELECT * FROM ossn_relationships WHERE(
			   		     relation_to='{$user}' AND
					     type='friend:request'
					     );");
	    $this->execute();
	    $from = $this->fetch(true);
        if(!is_object($from)){
		  return false; 
	     }
	    foreach($from  as $fr){
            if($this->isFriend($user,  $fr->relation_from)){
                $uss[] =  ossn_user_by_guid($fr->relation_from);
            }
		}
	   if(isset($uss)){
	   	   return $uss; 
	   }
	   return false;
	}
	/**
	* Send request to other user.
	*
	* @return bool;
	*/		
    public function sendRequest($from, $to){
	    $this->time = time();
	    $this->statement("INSERT INTO ossn_relationships (`relation_from`, `relation_to`, `time`, `type`)
					      VALUES('{$from}', '{$to}', '{$this->time}', 'friend:request');");
	     if($this->execute()){
		     return true;  
	     }
		 return false;
	}
	/**
	* Delete friend from list
	*
	* @return bool;
	*/		
   public function deleteFriend($from, $to){
	    $this->statement("DELETE FROM ossn_relationships WHERE(
						 relation_from='{$from}' AND relation_to='{$to}' OR 
						 relation_from='{$to}' AND relation_to='{$from}')");
	     if($this->execute()){
	         return true;  
	     }
		 return false;
	}
	/**
	* Get site users.
	*
	* @return object;
	*/		
   public function getSiteUsers(){
	$this->statement("SELECT * FROM ossn_users");
	$this->execute();
	return $this->fetch(true);
   }
  /**
	* Search users.
	*
	* @return object;
	*/		 
    public function SearchSiteUsers($q){
	$this->statement("SELECT * FROM ossn_users WHERE(first_name LIKE '%$q%' OR 
					 username LIKE '%$q%' OR email LIKE '%$q%' OR last_name LIKE '%$q%')");
	$this->execute();
	return $this->fetch(true);
   }
  /**
	* Update user last activity time
	*
	* @return bool;
	*/		 
   public function update_last_activity(){
	self::initAttributes();
	$user = ossn_loggedin_user();
	$guid = $user->guid;
	$params['table'] = 'ossn_users';
	$params['names'] = array('last_activity');
	$params['values'] = array(time());
	$params['wheres'] = array("guid='{$guid}'");
	if($guid > 0 && $this->OssnDatabase->update($params)){
	   return true;	
	}
	return false;
   }
  /**
	* Update user last login time.
	*
	* @return bool;
	*/		 
   public function update_last_login(){
	self::initAttributes();
	$user = ossn_loggedin_user();
	$guid = $user->guid;
	$params['table'] = 'ossn_users';
	$params['names'] = array('last_login');
	$params['values'] = array(time());
	$params['wheres'] = array("guid='{$guid}'");
	if($guid > 0 && $this->OssnDatabase->update($params)){
	   return true;	
	}
	return false;
   }
    /**
	* Get online site users.
	*
	* @params = $intervals => seconds 
	*
	* @return object;
	*/		 
   public function getOnline($intervals = '100'){
	self::initAttributes();
	$time = time();
	$params['from'] = 'ossn_users';
	$params['wheres'] = array("last_activity > {$time} - {$intervals}");  
	return $this->OssnDatabase->select($params, true);
   }
   /**
	* Count Total online site users.
	*
	* @return bool;
	*/		 
   public function online_total(){
	   return count($this->getOnline());
   }
  /**
   * Search site users with its entities
   *
   * @return bool;
   */ 
  public function searchUsers($q){
		$search = $this->SearchSiteUsers($q);
		
		$users = new OssnUser;
		foreach($search as $user){
		  $users->guid = $user->guid;
	      $userentity[] = $users->getUser();		
		}
		$data = $userentity;
		return $data;
	}
}//CLASS