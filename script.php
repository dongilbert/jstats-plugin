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
	}

	/**
	 * Generates a unique key to reduce stats duplication.
	 *
	 * @return string
	 */
	protected function generateUniqueId()
	{
		return md5(JFactory::getConfig()->get('secret') . time());
	}
}
