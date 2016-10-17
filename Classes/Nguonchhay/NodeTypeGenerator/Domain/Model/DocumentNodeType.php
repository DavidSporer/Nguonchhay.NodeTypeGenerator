<?php
namespace Nguonchhay\NodeTypeGenerator\Domain\Model;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/


use Nguonchhay\NodeTypeGenerator\Service\FileService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\Source\YamlSource;

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
	 * @param array $data
	 */
	public function generateConfig($data) {
		/* Define configuration content */
		$configContent = [
			'name' => $data['info']['sitePackage'] . ':' . ucwords($data['info']['name']),
			'superTypes' => isset($data['info']['superTypes']) ? $data['info']['superTypes'] : [],
			'label' => $data['info']['label'],
			'icon' => $data['info']['icon'],
			'group' => $data['info']['group']
		];
		$documentContent = $this->templateService->generateDocumentConfigTemplate($configContent, 'document');

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
			$documentContent[$configContent['name']]['ui']['help'] = $this->templateService->generateHelpMessageTemplate($data['info']['helperMessage']);
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
				'documentFilename' => $this->getTemplateFilename($arrSiteKeys[1]),
				'siteKey' => $arrSiteKeys[0],
				'content' => '',
				'properties' => '',
				'superTypes' => ''
			];

			/* SuperTypes to fusion */
			if (isset($this->content[$documentName]['superTypes'])) {
				$superTypes = $this->content[$documentName]['superTypes'];
				array_shift($superTypes);
				foreach ($superTypes as $superType => $value) {
					if (strpos('TYPO3.Neos.NodeTypes:TitleMixin', $superType) !== FALSE) {
						$params['superTypes'] .= 'title = ${q(node).property' . "('title')}\n\t\t";
					} else if (strpos('TYPO3.Neos.NodeTypes:TextMixin', $superType) !== FALSE) {
						$params['superTypes'] .= 'text = ${q(node).property' . "('text')}\n\t\t";
					} else if (strpos('TYPO3.Neos.NodeTypes:ImageMixin', $superType) !== FALSE) {
						$params['superTypes'] .= 'image = ${q(node).property' . "('image')}\n\t\t";
					} else if (strpos('TYPO3.Neos.NodeTypes:LinkMixin', $superType) !== FALSE) {
						$params['superTypes'] .= 'link = ${q(node).property' . "('link')}\n\t\t";
						$params['superTypes'] .= "link.@process.convertUris = TYPO3.Neos:ConvertUris";
					} else if (strpos('TYPO3.Neos.NodeTypes:ContentReferences', $superType) !== FALSE || strpos('TYPO3.Neos.NodeTypes:AssetList', $superType) !== FALSE) {
						$params['superTypes'] .= "<!--Add your fusion here-->\n\t\t";
					}
				}
			}

			/* Child nodes to fusion */
			if (isset($this->content[$documentName]['childNodes'])) {
				$content = "content {";
				foreach ($this->content[$documentName]['childNodes'] as $name => $childNode) {
					$content .= $this->getFusionContentTemplate(lcfirst($name));
				}
				$params['content'] .= $content . "}";
			}

			/* Properties to fusion */
			if (isset($this->content[$documentName]['properties'])) {
				foreach ($this->content[$documentName]['properties'] as $name => $property) {
					if ($name != 'layout') {
						$params['properties'] .= "\n\t\t" . $name . ' = ${q(node).property' . "('" . $name . "')}";

						if ($property['type'] == 'string' && isset($property['ui']['inspector']['editor']) && $property['ui']['inspector']['editor'] == 'TYPO3.Neos.NodeTypes:LinkMixin') {
							$params['properties'] .= "\n\t" . $name .'.@process.convertUris = TYPO3.Neos:ConvertUris';
						}
					}
				}
			}

			$fusionTemplate = FileService::read($this->getFusion());
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
				'imageNameSpace' => '',
				'content' => '',
				'properties' => '',
				'superTypes' => ''
			];

			/* SuperTypes to template */
			if (isset($this->content[$documentName]['superTypes'])) {
				$this->generateSuperTypesToTemplate($this->content[$documentName]['superTypes'], $params, true);
			}

			/* Child nodes to template */
			if (isset($this->content[$documentName]['childNodes'])) {
				foreach ($this->content[$documentName]['childNodes'] as $name => $childNode) {
					$params['content'] .= "{content." . lcfirst($name) . " -> f:format.raw()}\n\t\t\t";
				}
			}

			/* Display all properties of configuration to template */
			if (isset($this->content[$documentName]['properties'])) {
				$this->generatePropertiesToTemplate($this->content[$documentName]['properties'], $params, true);
			}

			$htmlTemplate = FileService::read($this->getTemplate());
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
