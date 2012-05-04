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

/**
 * CoreFramework_Controller_Plugin_Acl
 *
 * @author		Juan Pedro Gonzalez Gutierrez
 * @category	CoreFramework
 * @package		CoreFramework_Acl
 * @subpackage	Plugin
 * @copyright	Copyright (c) 2012 Juan Pedro Gonzalez Gutierrez
 * @license		http://www.gnu.org/licenses/gpl.html     GNU Public License v3.0
 */
class CoreFramework_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		try {
		$resource = $request->getModuleName() . ":" . $request->getControllerName();
		$action = $request->getActionName();
		
		$acl = new CoreFramework_Acl();
		
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			$role = "anonymous";
		} else {
			$user = Zend_Registry::get('Core_User');
			$role = $user->getRole();
		}
		
		if (!$acl->has($resource)) {
			// Administrators have full access (Even if we fogot to set a resource)
			if ($role != 'administrator') {
				$request->setModuleName('default');
		    	$request->setControllerName('error');
		    	$request->setActionName('error404');
		    	$request->isDispatched(false);
		    	return;
			}
		} elseif (!$acl->isAllowed($role, $resource, $action)) {
			$request->setModuleName('default');
	    	$request->setControllerName('error');
	    	$request->setActionName('error404');
	    	$request->isDispatched(false);
	    	return;
		}	
		} catch (Exception $ex) {
			echo $ex->getMessage();exit(0);
		}
	}
	
}