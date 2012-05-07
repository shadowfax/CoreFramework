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

class CoreFramework_Acl_Adapters_Database
{
	/**
     * Zend_Db_Adapter_Abstract object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
	protected $_db;
	
	public function __construct()
	{
		
		// Last but not least...
		if (is_null($this->_db)) $this->_db = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function getResources()
	{
		$select = new Zend_Db_Select($this->_db);
		$select->distinct(true);
		$select->from('acl_resources', array('module', 'controller'));
		$select->order('module ASC');
		$select->order('controller ASC');
	
		$rawResources = $this->_db->fetchAll($select);
		$resources = array();
		
		foreach ($rawResources as $rawResource) {
			$resources[] = empty($rawResource['controller']) ? $rawResource['module'] : $rawResource['module'] . ":" . $rawResource['controller'];
		}
		
		return $resources;
	}
	
	protected function _recursiveGetRoles($role, Array $roles, Array &$result)
	{
		if (!array_key_exists($role['name'], $result)) {
			$select = new Zend_Db_Select($this->_db);
			$select->from('acl_roles', array('id', 'name'));
			$select->joinInner('acl_roles_inheritance', 'acl_roles_inheritance.member_id = acl_roles.id');
			$select->where('acl_roles_inheritance.role_id=?', $role['id']);
			
			$members = $this->_db->fetchAll($select);
			$parents = array();
			
			// add any childs
			foreach ($members as $member) {
				$parents[] = $member['name'];
				$this->_recursiveGetRoles($member, $roles, $result);
			}
			
			$result[($role['name'])] = $parents;
		}
	}
	
	public function getRoles()
	{
		$retItems = array();
		
		$select = new Zend_Db_Select($this->_db);
		$select->from('acl_roles', array('id', 'name'));
		$roles = $this->_db->fetchAll($select);
		
		foreach ($roles as $role) {
			$this->_recursiveGetRoles($role, $roles, $retItems);
		}
		
		$roles = array();
		foreach ($retItems as $key => $value) {
			$roles[] = array(
				'role'		=> $key,
				'parents'	=> (count($value) == 0) ? NULL : $value
			);
		}
		
		return $roles;
	}
	
	
	
	public function getPermissions()
	{
		$select = new Zend_Db_Select($this->_db);
		$select->from('acl_permissions',array('type', 'action'));
		$select->joinInner('acl_resources', 'acl_resources.id = acl_permissions.resource_id', array('module', 'controller'));
		$select->joinInner('acl_roles', 'acl_roles.id = acl_permissions.role_id', array('role' => 'name'));		
		return $this->_db->fetchAll($select);
	}
}