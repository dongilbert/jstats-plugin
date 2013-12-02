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
		$this->cacheFile = JPATH_ROOT . '/cache/jstats.php';

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
		if (! class_exists('JHttpFactory'))
		{
			JLoader::register('JHttp', dirname(__FILE__) . '/src/joomla/http/http.php');
			JLoader::register('JHttpFactory', dirname(__FILE__) . '/src/joomla/http/factory.php');
			JLoader::register('JHttpResponse', dirname(__FILE__) . '/src/joomla/http/response.php');
			JLoader::register('JHttpTransport', dirname(__FILE__) . '/src/joomla/http/transport.php');
			JLoader::register('JHttpTransportCurl', dirname(__FILE__) . '/src/joomla/http/transport/curl.php');
			JLoader::register('JHttpTransportSocket', dirname(__FILE__) . '/src/joomla/http/transport/socket.php');
			JLoader::register('JHttpTransportStream', dirname(__FILE__) . '/src/joomla/http/transport/stream.php');
		}

		$http = JHttpFactory::getHttp();

		$data = array(
			'unique_id' => $this->params->get('unique_id'),
			'php_version' => PHP_VERSION,
			'db_type' => $this->db->name,
			'db_version' => $this->db->getVersion(),
			'cms_version' => JVERSION,
			'server_os' => php_uname('s') . ' ' . php_uname('r')
		);

		$uri = new JUri('http://jstats.dongilbert.net/submit');

		$status = $http->post($uri, $data);

		if ($status->code === 200)
		{
			$this->writeCacheFile();
		}
	}

	protected function writeCacheFile()
	{
		if (is_readable($this->cacheFile))
		{
			unlink($this->cacheFile);
		}

		$now = time();

		$php = <<<PHP
<?php defined('_JEXEC') or die;

return $now;
PHP;

		file_put_contents($this->cacheFile, $php);
	}
}
