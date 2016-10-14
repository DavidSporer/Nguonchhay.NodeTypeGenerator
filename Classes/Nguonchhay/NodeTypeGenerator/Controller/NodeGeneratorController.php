<?php
namespace Nguonchhay\NodeTypeGenerator\Controller;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/

use Nguonchhay\NodeTypeGenerator\Domain\Model\ContentNodeType;
use Nguonchhay\NodeTypeGenerator\Domain\Model\DocumentNodeType;
use Nguonchhay\NodeTypeGenerator\Service\FileService;
use TYPO3\Flow\Annotations as Flow;

class NodeGeneratorController extends AbstractController {

	const TEMP_PATH = FLOW_PATH_PACKAGES . 'Application/Nguonchhay.NodeTypeGenerator/Resources/Private/StaticTemplates/Temporary';

	/**
	 * @Flow\Inject
	 * @var DocumentNodeType
	 */
	protected $documentNodeType;

	/**
	 * @Flow\Inject
	 * @var ContentNodeType
	 */
	protected $contentNodeType;


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
			$this->documentNodeType->clearGenerateFiles();
			$this->documentNodeType->generateDocumentNodeType($arguments);
		} else {
			$this->contentNodeType->clearGenerateFiles();
			$this->contentNodeType->generateContentNodeType($arguments);
		}

		$this->redirect('confirm', null, null, ['isDocument' => $isDocument]);
	}

	/**
	 * @param boolean $isDocument
	 */
	public function confirmAction($isDocument) {
		$nodetype = [
			'config' => [],
			'fusion' => [],
			'template' => []
		];

		$files = glob(self::TEMP_PATH . '/*');
		$hasFile = false;
		foreach($files as $file) {
			if(is_file($file)) {
				$filename = basename($file);
				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				if ($extension == 'yaml') {
					$nodetype['config'] = [
						'id' => 'config',
						'filename' => $filename,
						'content' => FileService::read(self::TEMP_PATH . '/' . $filename)
					];
					$hasFile = true;
				} else if($extension == 'ts2') {
					$nodetype['fusion'] = [
						'id' => 'fusion',
						'filename' => $filename,
						'content' => FileService::read(self::TEMP_PATH . '/' . $filename)
					];
					$hasFile = true;
				} else if($extension == 'html') {
					$nodetype['template'] = [
						'id' => 'template',
						'filename' => $filename,
						'content' => FileService::read(self::TEMP_PATH . '/' . $filename)
					];
					$hasFile = true;
				}
			}
		}

		if (! $hasFile) {
			$this->redirect('generateForm');
		}

		$this->view->assign('isDocument', $isDocument);
		$this->view->assign('nodetype', $nodetype);
	}

	/**
	 * Copy all generated files to active site
	 */
	public function setupNodeTypeAction() {
		$arguments = $this->request->getArguments();
		$files = glob(self::TEMP_PATH . '/*');
		$baseDestination = FLOW_PATH_PACKAGES . 'Sites/' . $this->getActiveSiteKey();
		foreach($files as $file) {
			if(is_file($file)) {
				$filename = basename($file);
				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				if ($extension == 'yaml') {
					copy(self::TEMP_PATH . '/' . $filename, $baseDestination . '/Configuration/' . $filename);

					/* Enable inline-editable of document nodetype */
					if ($arguments['isDocument']) {
						$this->documentNodeType->generateInlineEditablePropertiesToTemplate($baseDestination . '/Configuration/' . basename($filename, '.yaml'));
					}
				} else if($extension == 'ts2') {
					$fusion = '';
					if ($arguments['isDocument']) {
						$fusionDestination = $baseDestination . '/Resources/Private/TypoScript/Root.ts2';
						$fusion = FileService::read($fusionDestination);
					} else {
						$fusionDestination = $baseDestination . '/Resources/Private/TypoScript/NodeTypes/' . $filename;
					}
					$fusion .= "\n" . $arguments['fusion'];
					FileService::write($fusionDestination, $fusion);
				} else if($extension == 'html') {
					if ($arguments['isDocument']) {
						$templateSite = $baseDestination . '/Resources/Private/Templates/Page';
					} else {
						$templateSite = $baseDestination . '/Resources/Private/Templates/NodeTypes';
					}
					FileService::write($templateSite . '/' . $filename, $arguments['template']);
				}
			}
		}
		$this->view->assign('isDocument', $arguments['isDocument']);
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
