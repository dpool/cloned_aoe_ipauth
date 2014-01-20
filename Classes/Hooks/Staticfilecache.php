<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 DEV <dev@aoemedia.de>, AOE media GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package aoe_ipauth
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_AoeIpauth_Hooks_Staticfilecache {

	/**
	 * @var Tx_AoeIpauth_Domain_Service_ContentService
	 */
	protected $contentService = NULL;

	/**
	 * UserFunc for the tx_ncstaticfilecache to avoid caching when a tt_content element on the page has user access settings set
	 *
	 * @param array $parameters
	 * @param tx_ncstaticfilecache $parent
	 */
	public function createFileInitializeVariables(array &$parameters, tx_ncstaticfilecache $parent) {

		$tsfe = $parameters['TSFE'];
		$staticallyCachable = $parameters['staticCacheable'];

		// Don't do anything if the whole page is already not statically cachable
		if (!$staticallyCachable) {
			return;
		}

		// Only check if this feature is enabled in config.
		$tsConfig = $tsfe->tmpl->setup['config.'];
		$isStaticAwarenessEnabled = (!empty($tsConfig) && isset($tsConfig['aoe_ipauth.']) && $tsConfig['aoe_ipauth.']['staticAwareness']);
		if (!$isStaticAwarenessEnabled) {
			return;
		}

		$pageId = $tsfe->id;
		$languageContent = $tsfe->sys_language_content;

		$pageHasUserAwareContent = $this->isPageUserCustomized($pageId, $languageContent);
		$allowStaticCaching = (!$pageHasUserAwareContent);

		$parameters['staticCacheable'] = $allowStaticCaching;
	}

	/**
	 * Uses service to check if a page is user customized
	 *
	 * @param int $pageId
	 * @param int $language
	 * @return bool
	 */
	protected function isPageUserCustomized($pageId, $language) {
		if (NULL === $this->contentService) {
			$this->contentService = t3lib_div::makeInstance('Tx_AoeIpauth_Domain_Service_ContentService');
		}
		return $this->contentService->isPageUserCustomized($pageId, $language);
	}
}
?>