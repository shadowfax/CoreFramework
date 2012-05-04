<?php


class CoreFramework_Acl extends Zend_Acl
{
	const ADAPTER		= 'db';
	//const TABLE_ROLES	= 'rolesTable';
	
	/**
     * Zend_Db_Adapter_Abstract object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    
	/**
     * Constructor
     *
     * $config is an instance of Zend_Config or an array of key/value pairs containing configuration options for
     * CoreFramework_Acl.
     * 
     * * Supported params for $config are:
     * - db              = user-supplied instance of database connector,
     *                     or key name of registry instance.
     * 
     * 
     */
	public function __construct($config = array())
	{
		if ($config instanceof Zend_Config) {
			$config = $config->toArray();
		} elseif (is_string($config)) {
			$config = array(self::ADAPTER => $config);
		} elseif (!is_array($config)) {
			/**
			 * @see CoreFramework_Acl_Exception
			 */
			require_once 'CoreFramework/Acl/Exception.php';
			throw new CoreFramework_Acl_Exception("$config must be an instance of Zend_Config or array of key/value pairs containing configuration options for CoreFramework_Acl.");
		}
        
		if ($config) {
			$this->setOptions($config);
		}
		
		// Load defaults if necesary
		$this->_setup();
		
        // Initialize ACL Roles
        $this->_initRoles();
        $this->_initResources();
		$this->_initRights();
	}
	
	
	
	/**
	 * Initialize ACL Resources
	 */
	protected function _initResources()
	{
		$select = new Zend_Db_Select($this->_db);
		$select->distinct(true);
		$select->from('acl_resources', array('module', 'controller'));
		$select->order('module ASC');
		$select->order('controller ASC');
	
		$rawResources = $this->_db->fetchAll($select);
		
		foreach ($rawResources as $rawResource) {
			if (empty($rawResource['controller'])) {
				$this->addResource($rawResource['module']);
			} else {
				if (!$this->has($rawResource['module'])) {
					$this->addResource($rawResource['module']);
				}
				$this->addResource($rawResource['module'] . ":" . $rawResource['controller'], $rawResource['module']);
			}
		}
	}
	
	/**
	 * Initialize ACL Access Rights
	 */
	protected function _initRights()
	{
		$select = new Zend_Db_Select($this->_db);
		$select->from('acl_rights', array('grant'));
		$select->joinInner('acl_resources', 'acl_rights.resource_id = acl_resources.id', array('module', 'controller', 'action'));
		$select->joinInner('acl_roles', 'acl_rights.role_id = acl_roles.id', array('role' => 'name'));
		$rawRights = $this->_db->fetchAll($select);
		
		foreach($rawRights as $rights) {
			$resource = $rights['module'];
			if (!empty($rights['controller'])) {
				$resource .= ":" . $rights['controller'];
			}
			
			$action = null;
			if (!empty($rights['action'])) {
				$action = $rights['action'];
			}
			
			if ($rights['grant'] === '1') {
				$this->allow($rights['role'], $resource, $action);
			} else {
				$this->deny($rights['role'], $resource, $action);
			}
		}
	}
	
	/**
	 * Initialize ACL roles
	 */
	protected function _initRoles()
	{
		$select = new Zend_Db_Select($this->_db);
		$select->from('acl_roles', array('id', 'name'));
		$rawRoles = $this->_db->fetchAll($select);
		
		$roles = array();
		
		foreach($rawRoles as $rawRole) {
			$roles[$rawRole['name']] = array();
			
			$select = new Zend_Db_Select($this->_db);
			$select->from('acl_roles', array('name'));
			$select->joinInner('acl_role_inheritance', 'acl_roles.id = acl_role_inheritance.parent_id', NULL);
			$select->where('acl_role_inheritance.role_id=?', $rawRole['id']);
			$rawParents = $this->_db->fetchAll($select);
			
			foreach ($rawParents as $rawParent) {
				$roles[$rawRole['name']][] = $rawParent['name'];
			}
			
			 
		}
		
		// Start loading all roles
		foreach ($roles as $key => $value)
		{
			// check if the role is already present
			if (!$this->hasRole($key)) {
				if (count($value) == 0) {
					$this->addRole($key);
					//echo "addRole('" . $key . "')); <br />";
				} else {
					// This role has inheritance!
					$this->_initRolesHelper($key, $value, $roles);
					if (!$this->hasRole($key)) {
						$this->addRole($key, $value);
						//echo "addRole('" . $key . "', array(" . implode(", ", $value) . ")); <br />";
					}
				}
			}
		}
		
		//$allRoles = $this->getRoles();
		//print_r($allRoles);
		//exit(0);
	}
	
	private function _initRolesHelper($role, Array $parents, Array $roles)
	{	
		//echo "Trying " . $role . "...<br />";
		foreach ($parents as $parent) {
			if (!$this->hasRole($parent)) {
				$roleParents = $roles[$parent];
				//echo "\tParent roles for " . $parent . ": " . count($roleParents) . "<br />";
			
				if (count($roleParents) == 0) {
					//echo "addRole('" . $parent . "', array(" . implode(", ", $roleParents) . ")); <br />";
					$this->addRole($parent, $roleParents);
					
				} else {
					$this->_initRolesHelper($parent, $roleParents, $roles);
					if (!$this->hasRole($parent)) {
						//echo "addRole('" . $parent . "', array(" . implode(", ", $roleParents) . ")); <br />";
						$this->addRole($parent, $roleParents);
					}				
				}
			}
		}
	}
	
	/**
     * @param  mixed $db Either an Adapter object, or a string naming a Registry key
     * @return Zend_Db_Table_Abstract Provides a fluent interface
     */
    protected function _setAdapter($db)
    {
        $this->_db = self::_setupAdapter($db);
        return $this;
    }
    
	/**
     * Turnkey for initialization of a table object.
     * Calls other protected methods for individual tasks, to make it easier
     * for a subclass to override part of the setup logic.
     *
     * @return void
     */
    protected function _setup()
    {
        $this->_setupDatabaseAdapter();
    }
    
	/**
     * @param  mixed $db Either an Adapter object, or a string naming a Registry key
     * @return Zend_Db_Adapter_Abstract
     * @throws Zend_Db_Table_Exception
     */
    protected static function _setupAdapter($db)
    {
        if (is_null($db) || empty($db)) {
            return null;
        }
        if (is_string($db)) {
            require_once 'Zend/Registry.php';
            $db = Zend_Registry::get($db);
        }
        if (!$db instanceof Zend_Db_Adapter_Abstract) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception('Argument must be of type Zend_Db_Adapter_Abstract, or a Registry key where a Zend_Db_Adapter_Abstract object is stored');
        }
        return $db;
    }
    
	/**
     * Initialize database adapter.
     *
     * @return void
     * @throws Zend_Db_Table_Exception
     */
    protected function _setupDatabaseAdapter()
    {
        if (! $this->_db) {
            $this->_db = Zend_Db_Table::getDefaultAdapter();
            if (!$this->_db instanceof Zend_Db_Adapter_Abstract) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception('No adapter found for ' . get_class($this));
            }
        }
    }
    
	/**
     * setOptions()
     *
     * @param array $options
     * @return CoreFramework_Acl
     */
	public function setOptions(Array $options)
	{
    	foreach ($options as $key => $value) {
            switch ($key) {
                case self::ADAPTER:
                    $this->_setAdapter($value);
                    break;
                default:
                    // ignore unrecognized configuration directive
                    break;
            }
        }

        return $this;
	}
}