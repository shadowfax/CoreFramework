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

class CoreFramework_Acl extends Zend_Acl
{	
	/**
     * A reference to the set session save handler
     *
     * @var CoreFramework_Acl_Adapters_Interface
     */
	protected $_adapter;
	
	
	public function __construct()
	{
		
		if (is_null($this->_adapter)) {
			$this->_adapter = new CoreFramework_Acl_Adapters_Database();
		}
		
		//
		$this->_init();
		$this->_initResources();
		$this->_initRoles();
		$this->_initPermissions();
	}
	
	protected function _init()
	{
		
	}
	
	/**
	 * Initialize ACL resources.
	 */
	protected function _initResources()
	{
		$resources = $this->_adapter->getResources();
		foreach($resources as $resource) {
			// If debugging is needed
			//echo "addResource(" . $resource . ")<br />";
			$this->addResource($resource);
		}
		
		return $resources;
	}
	
	/**
	 * Initialize ACL roles.
	 */
	protected function _initRoles()
	{
		$roles = $this->_adapter->getRoles();
		
		foreach ($roles as $role) {
			$this->addRole(new Zend_Acl_Role($role['role']), $role['parents']);
		}
	}
	
	/**
	 * Initialize ACL permissions.
	 */
	protected function _initPermissions()
	{
		foreach($this->_adapter->getPermissions() as $permission) {
			$resource = empty($permission['controller']) ? $permission['module'] : $permission['module'] . ":" . $permission['controller'];
			$action = empty($permission['action']) ? NULL : $permission['action'];
			
			
			if (strcasecmp($permission['type'], 'allow') == 0) {
				//$this->allow($role, $permission['resource'], $action, $asert);
				$this->allow($permission['role'], $resource, $action);
			} else {
				//$this->deny($role, $permission['resource'], $action, $asert);
				$this->deny($permission['role'], $resource, $action);
			}
		}
	}
	
}