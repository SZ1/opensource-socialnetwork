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
class OssnAnnotation extends OssnEntities{
	/**
	 * Initialize the objects.
	 *
	 * @return void;
	 */	
	public function initAttributes(){
			$this->OssnDatabase = new OssnDatabase;	
			$this->time_created = time();
			if(empty($this->subtype)){
			  $this->subtype = NULL;	
			}
	 if(empty($this->premission)){
	   $this->premission = OSSN_PUBLIC;
	 }
	 if(empty($this->order_by)){
	   $this->order_by = '';	
	 }
	}
	/**
	 * Create annotation;
	 *
	 * @requires : $object->(owner_guid, subject_guid, type, subtype, value)
	 *
	 * @return bool;
	 */		
	public function addAnnotation(){
		 self::initAttributes();
		 $params['into'] = 'ossn_annotations';
		 $params['names'] = array('owner_guid', 'subject_guid', 'type', 'time_created');
		 $params['values'] = array($this->owner_guid, $this->subject_guid , $this->type, $this->time_created);
		 $this->annotation_type = $this->type;
		 $this->owner_guid_old = $this->owner_guid;
		 if($this->OssnDatabase->insert($params)){
 		     $this->annotation_inserted = $this->OssnDatabase->getLastEntry();			 
			 $this->atype = $this->type;
			 $this->type = 'annotation';
			 $this->subtype = $this->atype;
			 $this->owner_guid = $this->annotation_inserted;
			 $this->value = $this->value;
			 $this->add();
			 
			$params['subject_guid'] = $this->subject_guid;
			$params['owner_guid'] = $this->owner_guid_old ;
			$params['type'] = $this->annotation_type;
			$params['annotation_guid'] = $this->OssnDatabase->getLastEntry();
			ossn_trigger_callback('annotations', 'created', $params);
			
			return true;
		 }
		 return false;
	}
	/**
	 * Get annotation by subject_guid;
	 *
	 * @requires : $object->(subject_guid, types(optional))
	 *
	 * @return annotations;
	 */		
	public function getAnnotationBySubject(){
		self::initAttributes();
		if(!empty($this->type)){
		   $type = "AND type='{$this->type}'";
		}
		$params['from'] = 'ossn_annotations';
		$params['wheres'] = array("subject_guid='{$this->subject_guid}' {$type}");
		$params['order_by'] = $this->order_by;
 		$annotations = $this->OssnDatabase->select($params, true);
		unset($this->order_by);
		foreach($annotations as $annotation){
				$this->owner_guid = $annotation->id;
		        $this->type = 'annotation';
		        $this->entities = $this->get_entities();
				foreach($this->entities as $entity){
		          $entities['value'] = $entity->value;
		         }
			$data[] = array_merge(get_object_vars($annotation), $entities);	 
		}
		return arrayObject($data, get_class($this));
	}
	/**
	 * Get annotation by annotation id;
	 *
	 * @requires : $object->(annotation_id)
	 *
	 * @return annotation;
	 */	
	public function getAnnotationById(){
		self::initAttributes();
		$params['from'] = 'ossn_annotations';
		$params['wheres'] = array("id='{$this->annotation_id}'");
		$params['order_by'] = $this->order_by;
 		$annotation = $this->OssnDatabase->select($params);
		unset($this->order_by);
		
		$this->owner_guid = $annotation->id;
		$this->type = 'annotation';
		$this->entities = $this->get_entities();
		foreach($this->entities as $entity){
		      $entities['value'] = $entity->value;
		 }
	    $data = array_merge(get_object_vars($annotation), $entities);	 
		return arrayObject($data, get_class($this));
	}
	/**
	 * Delete Annotation
	 *
	 * @params $annotation = annotation_id
	 *
	 * @return bool;
	 */		
	public function deleteAnnotation($annotation){
	   self::initAttributes();
	   if($this->deleteByOwnerGuid($annotation, 'annotation')){
		$this->statement("DELETE FROM ossn_annotations WHERE(guid='{$annotation}')");
		$this->execute();
		return true;
	   }
	   return false;
   }
  	/**
	 * Delete annotations by subject guid
	 *
	 * @params $subject = subject_guid,
	 *         $type = annotation type
	 *
	 * @return bool;
	 */	 
   	public function annon_delete_by_subject($subject, $type){
	   self::initAttributes();
	   $this->subject_guid = $subject;
	   $this->type = $type;
	   $annotations = $this->getAnnotationBySubject();
	   foreach($annotations as $annon){
	     $this->deleteByOwnerGuid($annon->id, 'annotation');
	   	 $this->statement("DELETE FROM ossn_annotations WHERE(id='{$annon->id}')");
		 $this->execute();
	     }
	  return true;
   }
  	/**
	 * Get newly create annoation id
	 *
	 * @return (int);
	 */	 
   public function getAnnotationId(){
	return $this->annotation_inserted;   
   }
}//class