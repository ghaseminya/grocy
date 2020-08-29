<?php

namespace Grocy\Controllers\Users;

use Grocy\Services\DatabaseService;
use LessQL\Result;

class User
{
	const PERMISSION_ADMIN = 'ADMIN';

	const PERMISSION_USERS = 'USERS';
	const PERMISSION_USERS_CREATE = 'USERS_CREATE';
	const PERMISSION_USERS_EDIT = 'USERS_EDIT';
	const PERMISSION_USERS_READ = 'USERS_READ';
	const PERMISSION_USERS_EDIT_SELF = 'USERS_EDIT_SELF';

	const PERMISSION_STOCK = 'STOCK';
	const PERMISSION_STOCK_PURCHASE = 'STOCK_PURCHASE';
	const PERMISSION_STOCK_CONSUME = 'STOCK_CONSUME';
	const PERMISSION_STOCK_INVENTORY = 'STOCK_INVENTORY';
	const PERMISSION_STOCK_TRANSFER = 'STOCK_TRANSFER';
	const PERMISSION_STOCK_OPEN = 'STOCK_OPEN';
	const PERMISSION_STOCK_EDIT = 'STOCK_EDIT';

	const PERMISSION_RECIPES = 'RECIPES';
	const PERMISSION_RECIPES_MEALPLAN = 'RECIPES_MEALPLAN';

	const PERMISSION_SHOPPINGLIST = 'SHOPPINGLIST';
	const PERMISSION_SHOPPINGLIST_ITEMS_ADD = 'SHOPPINGLIST_ITEMS_ADD';
	const PERMISSION_SHOPPINGLIST_ITEMS_DELETE = 'SHOPPINGLIST_ITEMS_DELETE';

	const PERMISSION_CHORES = 'CHORES';
	const PERMISSION_CHORE_TRACK_EXECUTION = 'CHORE_TRACK_EXECUTION';
	const PERMISSION_CHORE_UNDO_EXECUTION = 'CHORE_UNDO_EXECUTION';

	const PERMISSION_BATTERIES = 'BATTERIES';
	const PERMISSION_BATTERIES_TRACK_CHARGE_CYCLE = 'BATTERIES_TRACK_CHARGE_CYCLE';
	const PERMISSION_BATTERIES_UNDO_CHARGE_CYCLE = 'BATTERIES_UNDO_CHARGE_CYCLE';

	const PERMISSION_TASKS = 'TASKS';
	const PERMISSION_TASKS_UNDO_EXECUTION = 'TASKS_UNDO_EXECUTION';
	const PERMISSION_TASKS_MARK_COMPLETED = 'TASKS_MARK_COMPLETED';

	const PERMISSION_EQUIPMENT = 'EQUIPMENT';

	const PERMISSION_CALENDAR = 'CALENDAR';

	const PERMISSION_MASTER_DATA_EDIT = 'MASTER_DATA_EDIT';

	/**
	 * @var \LessQL\Database|null
	 */
	protected $db;

	public function __construct()
	{
		$this->db = DatabaseService::getInstance()->GetDbConnection();

	}

	protected function getPermissions(): Result
	{
		return $this->db->user_permissions_resolved()->where('user_id', GROCY_USER_ID);
	}

	public function hasPermission(string $permission): bool
	{
		// global $PERMISSION_CACHE;
		// if(isset($PERMISSION_CACHE[$permission]))
		//	return $PERMISSION_CACHE[$permission];
		return $this->getPermissions()->where('permission_name', $permission)->fetch() !== null;
	}

	public static function checkPermission($request, string ...$permissions): void
	{
		$user = new User();
		foreach ($permissions as $permission) {
			if (!$user->hasPermission($permission)) {
				throw new PermissionMissingException($request, $permission);
			}
		}

	}

	public function getPermissionList()
	{
		return $this->db->uihelper_user_permissions()->where('user_id', GROCY_USER_ID);
	}

	public static function hasPermissions(string ...$permissions)
	{
		$user = new User();
		foreach ($permissions as $permission) {
			if (!$user->hasPermission($permission)) {
				return false;
			}
		}
		return true;
	}

	public static function PermissionList()
	{
		$user = new User();
		return $user->getPermissionList();
	}
}
