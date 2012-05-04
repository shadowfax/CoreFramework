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
	 * @var unknown_type
	 */
	protected $_role;
	
   /*
    protected function init()
    {
    	try {
    		
    	
    	// If the user is not authenticated then return anonymous
    	if (!Zend_Auth::getInstance()->hasIdentity()) { 
    		$this->_role = "anonymous";
    		return $this;
    	}
    	
    	$identity = Zend_Auth::getInstance()->getIdentity();
    	if (is_null($identity)) {
    		$this->_role = "anonymous";
    		return $this;
    	}
    	
    	// Get ACL role
    	$select_role = new Zend_Db_Select($this->getAdapter());
		$select_role->from('acl_roles', array('name'));
		$select_role->joinInner('users', 'users.role = acl_roles.id');
		$select_role->where('users.id=?', $identity);
		$this->_role = $this->getAdapter()->fetchOne($select);
		
		// If something goes wrong set as anonymous
		if (!$this->_role) $this->_role = "anonymous";
		
		return $this;
		
    	} catch (Exception $e) {
    		echo $e->getMessage();exit(0);
    	}
    }
    */
	
	public function getIdentity()
	{
		if(is_null($this->_uid)) {
			$this->_uid = -1;
		
			if (!Zend_Auth::getInstance()->hasIdentity()) {
				return $this->_uid;
			}
			
			$identity = Zend_Auth::getInstance()->getIdentity();
			if (is_null($identity)) {
				return $this->_uid;
			}
		}
		
		return $identity->id;
	}
	
    public function getRole()
    {
    	$uid = $this->getIdentity();
    	
    	if (is_null($this->_role)) {
    		if ($uid < 1) {
    			$this->_role = "anonymous";
    			return $this->_role;
    		}
    		
    		// Get the role from the database
    		$select = new Zend_Db_Select($this->getAdapter());
			$select->from('acl_roles', array('name'));
			$select->joinInner('users', 'users.role_id = acl_roles.id');
			$select->where('users.id=?', $uid);
			$this->_role = $this->getAdapter()->fetchOne($select);
			
			// If something goes wrong set as anonymous
			if (!$this->_role) $this->_role = "anonymous";
    	}
    	
    	
    	return $this->_role;
    }
	
}