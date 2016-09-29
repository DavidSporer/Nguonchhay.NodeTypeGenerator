<?php
namespace Nguonchhay\NodeTypeGenerator\Controller;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/

use TYPO3\Flow\Annotations as Flow;

class NodeGeneratorController extends AbstractController {



	/**
	 * @return void
	 */
	public function generateFormAction() {
		$this->view->assign('siteKey', $this->getActiveSiteKey());
	}

	/**
	 * @return string
	 */
	public function getActiveSiteKey() {
		$activeSiteKey = '';
		$sitePath = FLOW_PATH_PACKAGES . 'Sites/*';
		$sites = array_filter(glob($sitePath), 'is_dir');
		$arraySites = explode('/', array_shift($sites));
		if (count($arraySites)) {
			$activeSiteKey = $arraySites[count($arraySites) - 1];
		}
		return $activeSiteKey;
	}
}
