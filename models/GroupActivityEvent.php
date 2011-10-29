<?php
/**
 * @package Plugins
 * @subpackage group_activity
 *
 * @author Christoph Wanasek <christoph.wanasek@hotmail.com>
 * @copyright Christoph Wanasek 2011
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */

if (!defined('IN_CMS')) { exit(); }

 
class GroupActivityEvent extends Record
	{
    const TABLE_NAME = 'group_activity_event';
	public $id;
	public $name;
	public $status;
	public $user;
	public $dateFrom;
	public $dateTo;
	public $created;
	public $edited;
	public $approved;
	
	/**
	* Perform a detailed query on the table.
	*/
	public static function find($args=array())
		{
		// Process & tidy up data from $args.
		$where = isset($args['where']) ? trim($args['where']) : false;
		$order = isset($args['order']) ? trim($args['order']) : false;
		$limit = isset($args['limit']) ? (int) $args['limit'] : false;
		$offset = isset($args['offset']) ? (int) $args['offset'] : false;

		// Prepare $arg data for SQL string.
		$bits = array(
			'where' => $where !== false ? "WHERE {$where}" : '',
			'order' => $order !== false ? "ORDER BY {$order}" : '',
			'limit' => $limit !== false ? "LIMIT {$limit}" : '',
			'offset' => $offset !== false ? "OFFSET {$offset}" : '',
			);

		// Construct the SQL string.
		$sql = "
			SELECT *
			FROM ".self::TABLE_NAME."
			{$bits['where']} {$bits['order']}
			{$bits['limit']} {$bits['offset']}
			";

		// Execute the query.
		$stmt = self::$__CONN__->prepare($sql);
		$stmt->execute();

		// Return a single object if limit is set to 1
		// or an array of objects if not.
		if ($limit == 1)
			{
			return $stmt->fetchObject('GroupActivityEvent');
			}
		else
			{
			$objects = array();
			while($obj = $stmt->fetchObject('GroupActivityEvent'))
				{
				$objects[] = $obj;
				}
			return $objects;
			}
		}

	}