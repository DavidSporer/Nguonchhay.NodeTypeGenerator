<?php
namespace Nguonchhay\NodeTypeGenerator\Service;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/

use TYPO3\Flow\Annotations as Flow;

class PropertyTemplateService {

	/**
	 * @Flow\InjectConfiguration(package="Nguonchhay.NodeTypeGenerator", path="nodeType")
	 * @var array
	 */
	protected $settingNodeTypes;



	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public function isPropertyTypesExist($type) {
		$exist = false;
		$propertyTypes = $this->settingNodeTypes['propertyTypes'];
		foreach ($propertyTypes as $key => $propertyType) {
			if ($type == $key) {
				$exist = true;
				break;
			}
		}
		return $exist;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function generateDateTimeProperty($data) {
		return [
			trim($data['name']) => [
				'type' => 'DateTime',
				'defaultValue' => trim($data['defaultValue']),
				'ui' => [
					'label' => trim($data['label']),
					'reloadIfChanged' => true,
					'inspector' => [
						'group' => isset($data['documentGroup']) ? trim($data['documentGroup']) : 'document',
						'position' => 50,
						'editorOptions' => [
							'format' => 'd.m.Y'
						]
					]
				]
			]
		];
	}

	public function generateStringProperty($data) {

	}

	/**
	 * This function is used to generate property whose type is in [boolean, integer, image, asset, asset list, reference(s)]
	 *
	 * @param string $type
	 * @param array $data
	 *
	 * @return array
	 */
	private function generateProperty($type, $data) {
		return [
			trim($data['name']) => [
				'type' => $type,
				'defaultValue' => trim($data['defaultValue']),
				'ui' => [
					'label' => trim($data['label']),
					'reloadIfChanged' => true,
					'inspector' => [
						'group' => isset($data['documentGroup']) ? trim($data['documentGroup']) : 'document',
						'position' => 50
					]
				]
			]
		];
	}

	/**
	 * @param string $type
	 * @param array $data
	 *
	 * @return array
	 */
	public function getPropertyTemplate($type, $data) {
		$propertyTemplate = [];
		$type = trim($type);
		if ($this->isPropertyTypesExist($type)) {
			if ($type == 'boolean' || $type == 'integer' || $type == 'reference' || $type == 'references' || $type == 'TYPO3\Media\Domain\Model\Asset' || $type == 'array<TYPO3\Media\Domain\Model\Asset>' || $type == 'TYPO3\Media\Domain\Model\ImageInterface') {
				$propertyTemplate = $this->generateProperty($type, $data);
			} else if ($type == 'DateTime') {
				$propertyTemplate = $this->generateDateTimeProperty($data);
			} else if ($type == 'string') {
				$propertyTemplate = $this->generateStringProperty($data);
			}
		}
		return $propertyTemplate;
	}
}
