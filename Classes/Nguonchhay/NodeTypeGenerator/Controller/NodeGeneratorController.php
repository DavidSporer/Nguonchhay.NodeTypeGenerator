<?php
namespace Nguonchhay\NodeTypeGenerator\Controller;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/

use Nguonchhay\NodeTypeGenerator\Domain\Model\DocumentNodeType;
use TYPO3\Flow\Annotations as Flow;

class NodeGeneratorController extends AbstractController {

	/**
	 * @Flow\Inject
	 * @var DocumentNodeType
	 */
	protected $documentNodeType;



	/**
	 * @return void
	 */
	public function generateFormAction() {
		$superTypes = $this->settings['nodeType']['superTypes'];
		$validators = $this->settings['nodeType']['validators'];
		$propertyTypes = $this->settings['nodeType']['propertyTypes'];
		$groups = $this->settings['nodeType']['groups'];
		$editors = $this->settings['nodeType']['editors'];

		$this->view->assign('siteKey', $this->getActiveSiteKey());
		$this->view->assign('superTypes', $superTypes);
		$this->view->assign('validators', $validators);
		$this->view->assign('propertyTypes', $propertyTypes);
		$this->view->assign('groups', $groups);
		$this->view->assign('editors', $editors);
		$this->view->assign('fontAwesomeLink', $this->settings['fontAwesome']);
	}

	/**
	 * @return void
	 */
	public function generatingAction() {
		$arguments = $this->request->getArguments();
		$isDocument = intval($arguments['info']['isDocument']);
		if ($isDocument) {
			$this->documentNodeType->generateDocumentNodeType($arguments);
		} else {

		}
		die();
		$this->redirect('confirm', null, null, ['isDocument' => $isDocument]);
	}

	public function confirm($isDocument) {
		die();
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
