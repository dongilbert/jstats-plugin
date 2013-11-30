<?php
/**
 * @copyright   Copyright (C) 2013 Don Gilbert. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Class plgSystemJstats
 */
class plgSystemJstats extends JPlugin
{
	/**
	 * @var JApplication
	 *
	 * @since 1.0
	 */
	protected $app;

	/**
	 * Path to the cache file
	 *
	 * @var string
	 *
	 * @since 1.0
	 */
	protected $cacheFile;

	/**
	 * @var JDatabaseDriver
	 *
	 * @since 1.0
	 */
	protected $db;

	/**
	 * Flag for whether or not to perform the check.
	 *
	 * @var bool
	 *
	 * @since 1.0
	 */
	protected $doCheck = false;

	/**
	 * Stats Plugin Constructor
	 *
	 * @param object $subject
	 * @param array  $config
	 *
	 * @since 1.0
	 */
	public function __construct(&$subject, $config = array())
	{
		$this->app = JFactory::getApplication();
		$this->db = JFactory::getDbo();
		$this->cacheFile = JPATH_CACHE . '/jstats.php';

		parent::__construct($subject, $config);
	}

	public function onAfterInitialise()
	{
		if (is_readable($this->cacheFile))
		{
			$checkedTime = include $this->cacheFile;

			if ($checkedTime < strtotime('-12 hours'))
			{
				$this->sendStats();
			}
		}
		else
		{
			$this->sendStats();
		}
	}

	protected function sendStats()
	{
		$http = JHttpFactory::getHttp();

		$data = array(
			'php_version' => PHP_VERSION,
			'db_type' => $this->db->name,
			'db_version' => $this->db->getVersion()
		);

		$uri = new JUri('http://localhost/GIT/jstats-server/www/submit');

		$status = $http->post($uri, $data);

		var_dump($status);die;
	}
}
