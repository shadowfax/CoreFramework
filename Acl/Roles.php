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
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Zend_Db_Table_Row_Abstract
 */
require_once 'Zend/Db/Table/Row/Abstract.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';


/**
 * CoreFramework_Acl_Roles
 *
 * @author		Juan Pedro Gonzalez Gutierrez
 * @category	CoreFramework
 * @package		CoreFramework_Acl
 * @subpackage	Roles
 * @copyright	Copyright (c) 2012 Juan Pedro Gonzalez Gutierrez
 * @license		http://www.gnu.org/licenses/gpl.html     GNU Public License v3.0
 */
class CoreFramework_Acl_Roles extends Zend_Db_Table_Abstract
{
	
	/**
     * Constructor
     *
     * $config is an instance of Zend_Config or an array of key/value pairs containing configuration options for
     * CoreFramework_Acl_Roles and Zend_Db_Table_Abstract. These are the configuration options for 
     * CoreFramework_Acl_Roles:
     * 
     * 
     */
	public function __construct($config)
	{
		if ($config instanceof Zend_Config) {
			$config = $config->toArray();
		} else if (!is_array($config)) {
			/**
             * @see Zend_Session_SaveHandler_Exception
             */
            require_once 'Zend/Session/SaveHandler/Exception.php';
            
            throw new Zend_Session_SaveHandler_Exception(
                '$config must be an instance of Zend_Config or array of key/value pairs containing '
              . 'configuration options for CoreFramework_Acl_Roles and Zend_Db_Table_Abstract.');
		}
		
		foreach ($config as $key => $value) {
            /*do {
                switch ($key) {
                    case self::PRIMARY_ASSIGNMENT:
                        $this->_primaryAssignment = $value;
                        break;
                    case self::MODIFIED_COLUMN:
                        $this->_modifiedColumn = (string) $value;
                        break;
                    case self::LIFETIME_COLUMN:
                        $this->_lifetimeColumn = (string) $value;
                        break;
                    case self::DATA_COLUMN:
                        $this->_dataColumn = (string) $value;
                        break;
                    case self::LIFETIME:
                        $this->setLifetime($value);
                        break;
                    case self::OVERRIDE_LIFETIME:
                        $this->setOverrideLifetime($value);
                        break;
                    default:
                        // unrecognized options passed to parent::__construct()
                        break 2;
                }
                unset($config[$key]);
            } while (false);*/
        }

        parent::__construct($config);
        
        // Load the main roles
        
	}
	
	/**
     * Adds a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  Zend_Acl_Role_Interface              $role
     * @param  Zend_Acl_Role_Interface|string|array $parents
     * @uses   Zend_Acl_Role_Registry::add()
     * @return Zend_Acl Provides a fluent interface
     */
    public function addRole($role, $parents = null)
    {
        if (is_string($role)) {
            $role = new Zend_Acl_Role($role);
        }

        if (!$role instanceof Zend_Acl_Role_Interface) {
            require_once 'Zend/Acl/Exception.php';
            throw new Zend_Acl_Exception('addRole() expects $role to be of type Zend_Acl_Role_Interface');
        }

		$data = array(
			'name'		=> $role->getRoleId(),
			'core'		=> 0,
			'created'	=> date('Y-m-d H:i:s')
		);
		
		// Insert the new role into the database
		self::insert($data);
    }
}