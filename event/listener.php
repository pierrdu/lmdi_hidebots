<?php
/**
*
* @package phpBB Extension - LMDI Hide robots
* @copyright (c) 2015 LMID - Pierre Duhem
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

static public function getSubscribedEvents ()
{
return array(
     'core.obtain_users_online_string_sql'	=>	'hide_bots',
);
}

/* @var \phpbb\controller\helper */
protected $helper;

/* @var \phpbb\template\template */
protected $template;

/** @var \phpbb\db\driver\driver_interface */
protected $db;

/**
* Constructor
*
* @param \phpbb\controller\helper			$helper	Controller helper object
* @param \phpbb\template					$template	Template object
* @param \phpbb\db\driver\driver_interface 	$db
*/
public function __construct (
	\phpbb\controller\helper $helper,
     \phpbb\template\template $template,
	\phpbb\db\driver\driver_interface $db
     )
{
$this->helper 		= $helper;
$this->template 	= $template;
$this->db			= $db;
}

public function hide_bots ($event)
{
$sql = $event['sql'];
// echo ($sql);
$search = "ORDER BY username_clean ASC";
$replace = "AND user_type <> " . USER_IGNORE . " ORDER BY username_clean ASC";
$sql = str_replace ($search, $replace, $sql);
// echo ($sql);
$event['sql'] = $sql;
}

}	// Fin de la classe listener

