<?php
namespace Nguonchhay\NodeTypeGenerator\Service;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/

use TYPO3\Flow\Annotations as Flow;

class TemplateService {

	/**
	 * @Flow\Inject
	 * @var PropertyTemplateService
	 */
	protected $propertyTemplateService;



	/**
	 * @param array $data
	 * @param string $nodeType
	 *
	 * @return array
	 */
	public function generateDocumentConfigTemplate($data, $nodeType = 'content') {
		$superTypes = [];
		if ($nodeType == 'content') {
			$superTypes['TYPO3.Neos:Content'] = true;
		} else if ($nodeType == 'document') {
			$superTypes['TYPO3.Neos.NodeTypes:Page'] = true;
		}

		foreach ($data['superTypes'] as $superType) {
			$superTypes[$superType] = true;
		}

		$uiKeys = ['label', 'icon', 'group'];
		$ui = [];
		foreach ($uiKeys as $uiKey) {
			$ui[$uiKey] = $data[$uiKey];
		}

		return [
			$data['name'] => [
				'superTypes' => $superTypes,
				'ui' => $ui
			]
		];
	}

	/**
	 * @param $childNodes
	 *
	 * @return array
	 */
	public function generateChildNodesTemplate($childNodes) {
		$childNodeItem = [];
		$childNodesArray = explode(PHP_EOL, $childNodes);
		foreach ($childNodesArray as $strChildNode) {
			$strChildNode = trim($strChildNode);
			$childNodeData = explode('=>', $strChildNode);
			if (count($childNodeData)) {
				$childNodeName = lcfirst(trim($childNodeData[0]));
				$childNodeItem[$childNodeName]['type'] = 'TYPO3.Neos:ContentCollection';
				if (count($childNodeData) > 1) {
					$childNodeConstraintArray = explode(',', trim($childNodeData[1]));
					if (count($childNodeConstraintArray)) {
						$childNodeItem[$childNodeName]['constraints']['nodeTypes']['*'] = false;
						foreach ($childNodeConstraintArray as $childNodeConstraint) {
							$childNodeItem[$childNodeName]['constraints']['nodeTypes'][trim($childNodeConstraint)] = true;
						}
					}
				}
			}
		}
		return $childNodeItem;
	}

	/**
	 * @param $groupName
	 * @param int $position
	 * @param string $icon
	 *
	 * @return array
	 */
	public function generateGroupTemplate($groupName, $position = 50, $icon = '') {
		return [
			'groups' => [
				$groupName => [
					'label' => ucfirst($groupName),
					'position' => $position,
					'icon' => $icon
				]
			]
		];
	}

	/**
	 * @param $message
	 *
	 * @return array
	 */
	public function generateHelpMessageTemplate($message) {
		return [
			'message' => $message
		];
	}

	/**
	 * @param $groupName
	 * @param $dataProperties
	 *
	 * @return array
	 */
	public function generateProperties($groupName, $dataProperties) {
		$properties = [];
		foreach ($dataProperties as $strProperty) {
			/* Replace ? to " of property */
			$adjustStrProperty = str_replace('?', '"', $strProperty);
			$arrProperty = json_decode($adjustStrProperty, true);
			if (isset($arrProperty['name']) && isset($arrProperty['label']) && isset($arrProperty['propertyType'])) {
				if ($groupName != '') {
					$arrProperty['documentGroup'] = $groupName;
				}

				$property = $this->propertyTemplateService->getPropertyTemplate($arrProperty['propertyType'], $arrProperty);
				if (count($property)) {
					$properties[] = $property;
				}
			}
		}
		return $properties;
	}
}
