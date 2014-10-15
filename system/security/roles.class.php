<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Security;


	/**
	 * Provides application wide authentication
	 *
	 * @package			PHPRum
	 * @subpackage		Security
	 * @author			Darnell Shinbine
	 */
	final class Roles
	{
		/**
		 * Constructor
		 *
		 * @return void
		 */
		private function __construct() {}


		/**
		 * returns true if current user is a member of the provide role
		 *
		 * @param   string	$roleIdentifier	role identifier
		 * @return  bool	true if user is a member of the provided role
		 */
		public static function isUserInRole( $roleIdentifier )
		{
			return in_array($roleIdentifier, Roles::getRolesForUser());
		}


		/**
		 * returns true if current user is a member of the provided roles
		 *
		 * @param   array	$roleIdentifiers	array of role identifiers
		 * @return  bool	true if user is a member of the provided role
		 */
		public static function isUserInRoles( array $roleIdentifiers )
		{
			foreach($roleIdentifiers as $roleIdentifier)
			{
				if(in_array($roleIdentifier, Roles::getRolesForUser()))
				{
					return true;
				}
			}
			return false;
		}


		/**
		 * get roles for current user
		 *
		 * @return  array	All roles asigned to current user
		 */
		public static function getRolesForUser()
		{
			$roles = array();

			// Get roles using memberships
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationMemberships as $membership )
			{
				if( $membership["username"] === Authentication::$identity )
				{
					$roles[] = $membership["role"];
				}
			}

			// Get roles using membership tables
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationMembershipsTables as $membership )
			{
				// connect to data source
				$da = null;
				if( isset( $membership['dsn'] )) {
					$da = \System\DB\DataAdapter::create( $membership['dsn'] );
				}
				else {
					$da = \System\Base\ApplicationBase::getInstance()->dataAdapter;
				}

				$ds = $da->prepare($membership['source'])->openDataSet();
				if( $ds )
				{
					$ds->filter( $membership['username-field'], '=', Authentication::$identity, true );
					foreach( $ds->rows as $role )
					{
						$roles[] = $role[$membership['role-field']];
					}
				}
			}

			// TODO: add LDAP roles
			// Get roles using ldap
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationMembershipsTables as $ldap )
			{
			}

			return $roles;
		}
	}
?>