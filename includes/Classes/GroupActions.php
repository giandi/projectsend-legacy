<?php
/**
 * Class that handles all the actions and functions that can be applied to
 * clients groups.
 *
 * @package		ProjectSend
 * @subpackage	Classes
 */

namespace ProjectSend\Classes;
use \PDO;

class GroupActions
{

	var $group = '';
    private $dbh;

    public function __construct()
    {
        global $dbh;
        $this->dbh = $dbh;
    }

	/**
	 * Validate the information from the form.
	 */
	function validate($arguments)
	{
        $validation = new \ProjectSend\Classes\Validation;

		global $json_strings;
		$this->state = array();

		$this->id = $arguments['id'];
		$this->name = $arguments['name'];

		/**
		 * These validations are done both when creating a new group and
		 * when editing an existing one.
		 */
		$validation->validate('completed',$this->name,$json_strings['validation']['no_name']);

		if ($validation->passed()) {
            $results = [
                'passed' => true
            ];
		}
		else {
            $results = [
                'passed' => false,
                'errors' => $validation->list_errors(),
            ];
        }
        
        return $results;
	}

	/**
	 * Create a new group.
	 */
	function create($arguments)
	{
		$this->state = array();

		/** Define the group information */
		$this->name = $arguments['name'];
		$this->description = $arguments['description'];
		$this->members = $arguments['members'];
		$this->ispublic = $arguments['public'];
		$this->public_token		= generateRandomString(32);
		$this->timestamp = time();

		$this->sql_query = $this->dbh->prepare("INSERT INTO " . TABLE_GROUPS . " (name, description, public, public_token, created_by)"
												." VALUES (:name, :description, :public, :token, :admin)");
		$this->sql_query->bindParam(':name', $this->name);
		$this->sql_query->bindParam(':description', $this->description);
		$this->sql_query->bindParam(':public', $this->ispublic, PDO::PARAM_INT);
		$this->sql_query->bindParam(':admin', CURRENT_USER_USERNAME);
		$this->sql_query->bindParam(':token', $this->public_token);
		$this->sql_query->execute();


		$this->id = $this->dbh->lastInsertId();
		$this->state['new_id'] = $this->id;
		$this->state['public_token'] = $this->public_token;

		/** Create the members records */
		if ( !empty( $this->members ) ) {
			foreach ($this->members as $this->member) {
				$this->sql_member = $this->dbh->prepare("INSERT INTO " . TABLE_MEMBERS . " (added_by,client_id,group_id)"
														." VALUES (:admin, :member, :id)");
				$this->sql_member->bindParam(':admin', CURRENT_USER_USERNAME);
				$this->sql_member->bindParam(':member', $this->member, PDO::PARAM_INT);
				$this->sql_member->bindParam(':id', $this->id, PDO::PARAM_INT);
				$this->sql_member->execute();
			}
		}

		if ($this->sql_query) {
			$this->state['query'] = 1;
		}
		else {
			$this->state['query'] = 0;
		}
		
		return $this->state;
	}

	/**
	 * Edit an existing group.
	 */
	function edit($arguments)
	{
		$this->state = array();

		/** Define the group information */
		$this->id = $arguments['id'];
		$this->name = $arguments['name'];
		$this->description = $arguments['description'];
		$this->members = $arguments['members'];
		$this->ispublic = $arguments['public'];
		$this->timestamp = time();

		/** SQL query */
		$this->sql_query = $this->dbh->prepare( "UPDATE " . TABLE_GROUPS . " SET name = :name, description = :description, public = :public WHERE id = :id" );
		$this->sql_query->bindParam(':name', $this->name);
		$this->sql_query->bindParam(':description', $this->description);
		$this->sql_query->bindParam(':public', $this->ispublic, PDO::PARAM_INT);
		$this->sql_query->bindParam(':id', $this->id, PDO::PARAM_INT);
		$this->sql_query->execute();

		/** Clean the memmbers table */
		$this->sql_clean = $this->dbh->prepare("DELETE FROM " . TABLE_MEMBERS . " WHERE group_id = :id");
		$this->sql_clean->bindParam(':id', $this->id, PDO::PARAM_INT);
		$this->sql_clean->execute();
		
		/** Create the members records */
		if (!empty($this->members)) {
			foreach ($this->members as $this->member) {
				$this->sql_member = $this->dbh->prepare("INSERT INTO " . TABLE_MEMBERS . " (added_by,client_id,group_id)"
														." VALUES (:admin, :member, :id)");
				$this->sql_member->bindParam(':admin', CURRENT_USER_USERNAME);
				$this->sql_member->bindParam(':member', $this->member, PDO::PARAM_INT);
				$this->sql_member->bindParam(':id', $this->id, PDO::PARAM_INT);
				$this->sql_member->execute();
			}
		}

		if ($this->sql_query) {
			$this->state['query'] = 1;
		}
		else {
			$this->state['query'] = 0;
		}
		
		return $this->state;
	}

	/**
	 * Delete an existing group.
	 */
	function delete($group)
	{
		$this->check_level = array(9,8);
		if (isset($group)) {
			// Do a permissions check
			if (isset($this->check_level) && current_role_in($this->check_level)) {
				$this->sql = $this->dbh->prepare('DELETE FROM ' . TABLE_GROUPS . ' WHERE id=:id');
				$this->sql->bindParam(':id', $group, PDO::PARAM_INT);
				$this->sql->execute();
			}
		}
    }
}
