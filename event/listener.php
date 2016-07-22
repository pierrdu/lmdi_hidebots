<?php
/**
*
* @package phpBB Extension - LMDI Hide robots
* @copyright (c) 2015 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\hidebots\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{

	/* @var \phpbb\config\config */
	protected $config;

	/**
	* Constructor
	*
	*/
	public function __construct (
		\phpbb\config\config $config
		)
	{
		$this->config	= $config;
	}

	static public function getSubscribedEvents ()
	{
		return array(
			'core.obtain_users_online_string_sql'	=>	'hide_bots',
		);
	}

	function get_robots_group ()
	{
		global $table_prefix, $db;
		$sql = "SELECT group_id from ${table_prefix}groups where group_name = 'BOTS'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$code = $row['group_id'];
		return ((int) $code);
	}

	public function hide_bots ($event)
	{
		if (version_compare ($this->config['version'], '3.1.7', '>='))
		{
			$sql_ary = $event['sql_ary'];
			$code = $this->get_robots_group ();
			$where = " AND u.group_id <> $code ";
			$sql_ary['WHERE'] .= $where;
			$event['sql_ary'] = $sql_ary;
		}
		else		// >= 3.1.4 and < 3.1.7
		{
			$sql = $event['sql'];
			$search = "ORDER BY username_clean ASC";
			$replace = "AND user_type <> " . USER_IGNORE . " ORDER BY username_clean ASC";
			$sql = str_replace ($search, $replace, $sql);
			$event['sql'] = $sql;
		}
	}

}
