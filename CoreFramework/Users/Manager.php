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

class CoreFramework_Users_Manager extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';
	
	
	public function findUserByName($name)
	{
		
	}
	
}