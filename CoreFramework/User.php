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

class CoreFramework_User 
{	
	/**
	 * 
	 */
	protected $_adapter;
	
	/**
	 * Unique user identifier.
	 * @var int
	 */
	protected $_uid;
	
	/**
	 * User role
	 * @var string
	 */
	protected $_role;
	
	/**
	 * Singleton object.
	 * @var CoreFramework_User
	 */
	private static $_instance = NULL;
	
	
	protected function __construct()
	{
		// ToDo: Create an adapter, right now using default DB
		$this->_adapter = Zend_Db_Table::getDefaultAdapter();
		
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
		try {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;	
		} catch (Exception $e) {
			echo $e->message();exit(0);
		}
		
	}
	
	public function getIdentity()
	{
		return $this->_uid;
	}
	
	/**
	 * Returns the user role.
	 */
    public function getRole()
    {
    	try {
    	if (is_null($this->_role)) {
    		if ($this->_uid > 0) {
    			$select = new Zend_Db_Select($this->_adapter);
    		
    			$select->from('acl_roles', array('name'));
    			$select->joinInner('users', 'acl_roles.id = users.role_id', NULL);
    			$select->where('users.id=?', $this->_uid);
    			
    			$this->_role = $this->_adapter->fetchOne($select);
    			
    			if (empty($this->_role)) $this->_role = "anonymous";
    		} else {
    			$this->_role = "anonymous";
    		}
    	}	
    	} catch (Exception $e) {
    		echo $e->message();exit(0);
    	}
    	
    	return $this->_role;
    }
	
}