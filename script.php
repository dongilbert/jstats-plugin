<?php
/**
 * @copyright   Copyright (C) 2013 Don Gilbert. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Class plgSystemJstatsInstallerScript
 */
class plgSystemJstatsInstallerScript
{
	public function postflight($type, $parent)
	{
		$this->removeCacheFile();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$data = json_encode(array(
			'unique_id' => $this->generateUniqueId()
		));

		// Enable and set a unique id
		$query
			->update('#__extensions')
			->set('params = ' . $db->quote($data))
			->set('enabled = 1')
			->where('name = "plg_system_jstats"');

		$db->setQuery($query)->execute();
	}

	public function uninstall($parent)
	{
		$this->removeCacheFile();
	}

	/**
	 * Remove the cache file on uninstall and upgrade.
	 *
	 * @since 1.0
	 */
	protected function removeCacheFile()
	{
		$cacheFile = JPATH_ROOT . '/cache/jstats.php';

		if (is_readable($cacheFile))
		{
			unlink($cacheFile);
		}
	}

	/**
	 * Generates a unique key to reduce stats duplication.
	 *
	 * @return string
	 * 
	 * @since 1.0
	 */
	protected function generateUniqueId()
	{
		return md5(JFactory::getConfig()->get('secret') . time());
	}
}
