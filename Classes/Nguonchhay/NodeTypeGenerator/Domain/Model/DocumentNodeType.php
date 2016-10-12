<?php
namespace Nguonchhay\NodeTypeGenerator\Domain\Model;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/


use Nguonchhay\NodeTypeGenerator\Service\FileService;
use TYPO3\Flow\Annotations as Flow;

class DocumentNodeType extends AbstractNodeType {

	const PREFIX_DOCUMENT_NODETYPE = 'Documents';



	public function __construct() {
		parent::__construct('document');
		$this->content = [];
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function getConfigFilename($name) {
		$documentNodeTypeFilename = self::PREFIX_NODETYPE_FILENAME . '.' . self::PREFIX_DOCUMENT_NODETYPE . '.' . ucfirst($name);
		return $documentNodeTypeFilename;
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function getFusionFilename($name) {
		return ucfirst($name) . self::NODETYPE_FUSION_EXTENSION;
	}

	public function getTemplateFilename($name) {
		return ucfirst($name) . self::NODETYPE_TEMPLATE_EXTENSION;
	}

	/**
	 * @param array $data
	 */
	public function generateConfig($data) {
		/* Define configuration content */
		$configContent = [
			'name' => $data['info']['sitePackage'] . ':' . $data['info']['label'],
			'superTypes' => isset($data['info']['superTypes']) ? $data['info']['superTypes'] : [],
			'label' => $data['info']['label'],
			'icon' => $data['info']['icon'],
			'group' => $data['info']['group']
		];
		$documentContent = $this->templateService->generateDocumentConfigTemplate($configContent);

		/* Generate child nodes */
		if (isset($data['info']['childNodes']) && $data['info']['childNodes'] != '') {
			$childNodes = $this->templateService->generateChildNodesTemplate($data['info']['childNodes']);
			if (count($childNodes)) {
				$documentContent[$configContent['name']]['childNodes'] = $childNodes;
			}
		}

		$groupName = '';
		/* Generate group */
		if (isset($data['info']['groupName']) && $data['info']['groupName'] != '') {
			$groupName = trim($data['info']['groupName']);
			$documentContent[$configContent['name']]['ui']['inspector'] = $this->templateService->generateGroupTemplate($groupName);
		}

		/* Generate help message */
		if (isset($data['info']['helperMessage']) && $data['info']['helperMessage'] != '') {
			$documentContent[$configContent['name']]['ui']['helper'] = $this->templateService->generateHelpMessageTemplate($data['info']['helperMessage']);
		}

		/* Generate properties */
		$documentContent[$configContent['name']]['properties']['layout']['defaultValue'] = lcfirst($data['info']['name']);
		if (isset($data['properties']) && count($data['properties'])) {
			$properties = $this->templateService->generateProperties($groupName, $data['properties']);
			if (count($properties)) {
				foreach ($properties as $property) {
					$name = key($property);
					$documentContent[$configContent['name']]['properties'][$name] = $property[$name];
				}
			}
		}

		/* Create document nodetype file */
		$this->yamlSource->save($this->getTemporaryPath() . '/' . $this->getConfigFilename($data['info']['name']), $documentContent);
		$this->content = $documentContent;
	}

	/**
	 * Generate fusion (typoscript) file
	 */
	public function generateFusion() {
		if (count($this->content)) {
			$documentName = key($this->content);
			/* Prepare params to replace funsion template contents */
			$arrSiteKeys = explode(':', $documentName);
			$params = [
				'documentLayout' => lcfirst($arrSiteKeys[1]),
				'documentFilename' => ucfirst($arrSiteKeys[1]) . '.html',
				'siteKey' => $arrSiteKeys[0],
				'content' => ''
			];

			/* Child nodes fusion */
			if (isset($this->content[$documentName]['childNodes'])) {
				$content = "content {";
				foreach ($this->content[$documentName]['childNodes'] as $name => $childNode) {
					$content .= $this->getFusionContentTemplate(lcfirst($name));
				}
				$params['content'] = $content . "}";
			}

			$fusionTemplateFilenameAndPath = self::BASE_PATH . '/Document/Document.ts2';
			$fusionTemplate = FileService::read($fusionTemplateFilenameAndPath);
			if ($fusionTemplate != '') {
				foreach ($params as $key => $value) {
					$fusionTemplate = str_replace('###' . $key . '###', $value, $fusionTemplate);
				}
			}

			/* Create fusion file */
			FileService::write($this->getTemporaryPath() . '/' . $this->getFusionFilename($arrSiteKeys[1]), $fusionTemplate);
		}
	}

	/**
	 * Generate html template base on generated document nodetype
	 */
	public function generateTemplate() {
		if (count($this->content)) {
			$documentName = key($this->content);
			/* Prepare params to replace html template contents */
			$arrSiteKeys = explode(':', $documentName);
			$params = [
				'content' => '',
				'properties' => ''
			];

			/* Child nodes fusion */
			if (isset($this->content[$documentName]['childNodes'])) {
				foreach ($this->content[$documentName]['childNodes'] as $name => $childNode) {
					$params['content'] .= "{content." . lcfirst($name) . " -> f:format.raw()}\n\t\t\t";
				}
			}

			/* Display all properties of configuration to template */
			if (isset($this->content[$documentName]['properties'])) {
				foreach ($this->content[$documentName]['properties'] as $name => $property) {
					if ($name != 'layout') {
						$type = $property['type'];
						if ($type == 'integer') {
							$params['properties'] .= "\n {$name}";
						} else if ($type == 'string') {
							if (isset($property['ui']['inlineEditable'])) {
								$params['properties'] .= "\n\t\t\t<div{attributes -> f:format.raw()}>\n\t\t\t\t{neos:contentElement.editable(property: '$name')}\n\t\t\t</div>";
							} else {
								$params['properties'] .= "\n {$name}";
							}
						} else if ($type == 'DateTime') {
							$params['properties'] .= "\n\t\t\t" . '<f:if condition="{' . $name . '}"><f:format.date format="' . $property['ui']['inspector']['editorOptions']['format'] . '">{' . $name . '}</f:format.date></f:if>';
						} else if ($type == 'TYPO3\Media\Domain\Model\ImageInterface') {
							$params['properties'] .= "\n\t\t\t" . '<f:if condition="{' . $name . '}">' . "\t\t\t\t" . '<media:image asset="{' . $name . '}" alt="{alternativeText}" title="{title}" width="{width}" maximumWidth="{maximumWidth}" height="{height}" maximumHeight="{maximumHeight}" allowUpScaling="{allowUpScaling}" allowCropping="{allowCropping}" />' . "\t\t\t" . '</f:if>';
						}
					}
				}
			}

			$htmlTemplateFilenameAndPath = self::BASE_PATH . '/Document/Document.html';
			$htmlTemplate = FileService::read($htmlTemplateFilenameAndPath);
			if ($htmlTemplate != '') {
				foreach ($params as $key => $value) {
					$htmlTemplate = str_replace('###' . $key . '###', $value, $htmlTemplate);
				}
			}

			/* Create fusion file */
			FileService::write($this->getTemporaryPath() . '/' . $this->getTemplateFilename($arrSiteKeys[1]), $htmlTemplate);
		}
	}

	/**
	 * @param $data
	 */
	public function generateDocumentNodeType($data) {
		$this->generateConfig($data);
		$this->generateFusion();
		$this->generateTemplate();
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function getFusionContentTemplate($name) {
		return "
			$name = ContentCollection {
				nodePath = '$name'
			}
		";
	}
}
