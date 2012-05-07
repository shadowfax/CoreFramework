<?php
/**
 * Core Framework
 *
 * Copyright (c) 2012 Juan Pedro Gonzalez Gutierrez.
 * 
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Public License v3.0
 * which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/gpl.html
 *
 * Contributors:
 *    Juan Pedro Gonzalez Gutierrez - initial API and implementation
 *
 */

class CoreFramework_User extends Zend_Db_Table_Abstract
{	
	protected $_name = 'users';
	protected $_uid;
	
	/**
	 * User roles
	 * @var Array
	 */
	protected $_roles;
	
	/**
	 * Singleton object.
	 * @var CoreFramework_User
	 */
	private static $_instance;
	
	
	protected function __construct()
	{
		// Set as anonymous by default
		$this->_uid = -1;
		
		// Now try figuring out if the user has a valid ID.
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$identity = Zend_Auth::getInstance()->getIdentity();
			if (!is_null($identity)) {
				$this->_uid = $identity->id;
			}
		}
	}
	
   /**
    * Singletone method.
    * @return CoreFramework_User
    */
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getIdentity()
	{
		return $this->_uid;
	}
	
	/**
	 * Returns the user roles.
	 */
    public function getRoles()
    {
    	if (is_null($this->_roles)) {
    		if ($this->_uid > 0) {
    			$select = new Zend_Db_Select($this->getAdapter());
    		
    			$select->from('acl_groups', array('name'));
    			$select->joinInner('acl_groups_users', 'acl_groups.id = acl_groups_users.group_id', NULL);
    			$select->where('acl_groups_users.user_id=?', $this->_uid);
    			
    			$this->_roles = array();
    			foreach($rawRoles as $roles) {
    				$this->_roles[] = $roles['name'];
    			}
    			
    			if(count($this->_roles) === 0) $this->_roles = array("anonymous");
    			else $this->_roles[] = "UID:" . $this->_uid;
    		} else {
    			$this->_roles = array("anonymous");
    		}
    	}
    	return $this->_roles;
    }
	
}