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
	 * @param $property
	 * @param $validators
	 *
	 * @return mixed
	 */
	public function assignValidatorProperty(&$property, $validators) {
		$name = key($property);
		foreach ($validators as $validator) {
			$validator = trim($validator);
			$property[$name]['validation'][$validator] = [];

			if ($validator == 'TYPO3.Neos/Validation/NotEmptyValidator') {
				unset($property[$name]['ui']['inspector']['editorOptions']['allowEmpty']);
			}
		}
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function generateDateTimeProperty($data) {
		$name = lcfirst(trim($data['name']));
		$datetimeProperty[$name] = [
			'type' => 'DateTime',
			'defaultValue' => $data['defaultValue'],
			'ui' => [
				'label' => $data['label'],
				'reloadIfChanged' => true,
				'inspector' => [
					'group' => isset($data['documentGroup']) ? trim($data['documentGroup']) : 'document',
					'position' => 50,
					'editorOptions' => [
						'format' => 'd.m.Y'
					]
				]
			]
		];
		$this->assignValidatorProperty($datetimeProperty, $data['validators']);

		return $datetimeProperty;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function generateInlineEditableProperty($data) {
		return [
			lcfirst(trim($data['name'])) => [
				'type' => 'string',
				'defaultValue'=> $data['defaultValue'],
				'ui' => [
					'inlineEditable' => true,
					'aloha' => [
						'placeholder' => $data['placeholder'],
						'autoparagraph' => true,
						'format' => [
							'strong' => true,
							'em' => true,
							'u' => false,
							'sub' => false,
							'sup' => false,
							'del' => false,
							'p' => false,
							'h1' => true,
							'h2' => true,
							'h3' => true,
							'pre' => true,
							'removeFormat' => true
						],
						'table' => [
							'table' => true
						],
						'list' => [
							'ol' => true,
							'ul' => true,
							'link' => true,
							'a' => true
						]
					]
				]
			]
		];
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function generateSingleTextProperty($data) {
		$name = lcfirst(trim($data['name']));
		$singleTextProperty[$name] = [
			'type' => 'string',
			'ui' => [
				'label' => $data['label'],
				'reloadIfChanged' => true,
				'inspector' => [
					'group' => $data['group'],
					'editorOptions' => [
						'maxlength' => 255
					]
				]
			]
		];
		$this->assignValidatorProperty($singleTextProperty, $data['validators']);

		return $singleTextProperty;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function generateTextAreaProperty($data) {
		$name = lcfirst(trim($data['name']));
		$textAreaProperty[$name] = [
			'type' => 'string',
			'ui' => [
				'label' => $data['label'],
				'reloadIfChanged' => true,
				'inspector' => [
					'group' => $data['group'],
					'editor' => 'TYPO3.Neos/Inspector/Editors/TextAreaEditor',
					'editorOptions' => [
						'rows' => intval($data['rows'])
					]
				]
			]
		];
		$this->assignValidatorProperty($textAreaProperty, $data['validators']);

		return $textAreaProperty;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function generateSelectProperty($data) {
		$name = lcfirst(trim($data['name']));
		$selectProperty[$name] = [
			'type' => 'string',
			'defaultValue' => $data['defaultValue'],
			'ui' => [
				'label' => $data['label'],
				'inspector' => [
					'group' => $data['group'],
					'editor' => 'TYPO3.Neos/Inspector/Editors/SelectBoxEditor',
					'editorOptions' => [
						'allowEmpty' => true,
						'placeholder' => 'Select ' . $name . ' options'
					]
				]
			]
		];

		$arrOptions = explode(PHP_EOL, $data['options']);
		if (count($arrOptions)) {
			foreach ($arrOptions as $option) {
				$arrOption = explode(':', trim($option));
				if (count($arrOption)) {
					$selectProperty[$name]['ui']['inspector']['editorOptions']['values'][trim($arrOption[0])] = [
						'label' => trim($arrOption[1])
					];
				}
			}
		}
		$this->assignValidatorProperty($selectProperty, $data['validators']);

		return $selectProperty;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function generateStringProperty($data) {
		$property = [];
		if ($data['type']['inlineEditable']['isInlineEditable']) {
			$adjustData = [
				'name' => $data['name'],
				'defaultValue' => $data['defaultValue'],
				'placeholder' => $data['type']['inlineEditable']['placeholder']
			];
			$property = $this->generateInlineEditableProperty($adjustData);
		} else if ($data['type']['editorType'] == 'default') {
			$adjustData = [
				'name' => $data['name'],
				'label' => $data['label'],
				'defaultValue' => $data['defaultValue'],
				'placeholder' => $data['type']['editorText']['placeholder'],
				'group' => isset($data['documentGroup']) ? trim($data['documentGroup']) : 'document',
				'validators' => $data['validators']
			];
			$property = $this->generateSingleTextProperty($adjustData);
		} else if ($data['type']['editorType'] == 'TYPO3.Neos/Inspector/Editors/TextAreaEditor') {
			$adjustData = [
				'name' => $data['name'],
				'label' => $data['label'],
				'defaultValue' => $data['defaultValue'],
				'rows' => $data['type']['editorTextArea']['rows'],
				'group' => isset($data['documentGroup']) ? trim($data['documentGroup']) : 'document',
				'validators' => $data['validators']
			];
			$property = $this->generateTextAreaProperty($adjustData);
		} else if ($data['type']['editorType'] == 'TYPO3.Neos/Inspector/Editors/SelectBoxEditor') {
			$adjustData = [
				'name' => $data['name'],
				'label' => $data['label'],
				'defaultValue' => $data['defaultValue'],
				'options' => $data['type']['editorSelect']['options'],
				'group' => isset($data['documentGroup']) ? trim($data['documentGroup']) : 'document',
				'validators' => $data['validators']
			];
			$property = $this->generateSelectProperty($adjustData);
		}

		return $property;
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
		$name = lcfirst(trim($data['name']));
		$property[$name] = [
			'type' => $type,
			'defaultValue' => $data['defaultValue'],
			'ui' => [
				'label' => $data['label'],
				'reloadIfChanged' => true,
				'inspector' => [
					'group' => isset($data['documentGroup']) ? trim($data['documentGroup']) : 'document',
					'position' => 50
				]
			]
		];
		$this->assignValidatorProperty($property, $data['validators']);

		return $property;
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
