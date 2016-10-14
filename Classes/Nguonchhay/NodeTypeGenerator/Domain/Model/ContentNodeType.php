<?php
namespace Nguonchhay\NodeTypeGenerator\Domain\Model;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/


use Nguonchhay\NodeTypeGenerator\Service\FileService;
use TYPO3\Flow\Annotations as Flow;

class ContentNodeType extends AbstractNodeType {

	const PREFIX_DOCUMENT_NODETYPE = 'Contents';



	public function __construct() {
		parent::__construct('content');
		$this->content = [];
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function getConfigFilename($name) {
		$contentNodeTypeFilename = self::PREFIX_NODETYPE_FILENAME . '.' . self::PREFIX_DOCUMENT_NODETYPE . '.' . ucfirst($name);
		return $contentNodeTypeFilename;
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
		$content = $this->templateService->generateDocumentConfigTemplate($configContent, 'content');

		/* Generate child nodes */
		if (isset($data['info']['childNodes']) && $data['info']['childNodes'] != '') {
			$childNodes = $this->templateService->generateChildNodesTemplate($data['info']['childNodes']);
			if (count($childNodes)) {
				$content[$configContent['name']]['childNodes'] = $childNodes;
			}
		}

		$groupName = '';
		/* Generate group */
		if (isset($data['info']['groupName']) && $data['info']['groupName'] != '') {
			$groupName = trim($data['info']['groupName']);
			$content[$configContent['name']]['ui']['inspector'] = $this->templateService->generateGroupTemplate($groupName);
		}

		/* Generate help message */
		if (isset($data['info']['helperMessage']) && $data['info']['helperMessage'] != '') {
			$content[$configContent['name']]['ui']['help'] = $this->templateService->generateHelpMessageTemplate($data['info']['helperMessage']);
		}

		/* Generate properties */
		if (isset($data['properties']) && count($data['properties'])) {
			$properties = $this->templateService->generateProperties($groupName, $data['properties']);
			if (count($properties)) {
				foreach ($properties as $property) {
					$name = key($property);
					$content[$configContent['name']]['properties'][$name] = $property[$name];
				}
			}
		}

		/* Create content nodetype file */
		$this->yamlSource->save($this->getTemporaryPath() . '/' . $this->getConfigFilename($data['info']['name']), $content);
		$this->content = $content;
	}

	/**
	 * Generate fusion (typoscript) file
	 */
	public function generateFusion() {
		if (count($this->content)) {
			$contentName = key($this->content);
			/* Prepare params to replace funsion template contents */
			$arrSiteKeys = explode(':', $contentName);
			$params = [
				'superType' => 'TYPO3.Neos:Content',
				'content' => $contentName,
				'contentFilename' => ucfirst($arrSiteKeys[1]) . '.html',
				'siteKey' => $arrSiteKeys[0],
				'class' => strtolower($arrSiteKeys[1]),
				'childNodes' => '',
				'properties' => '',
				'superTypes' => ''
			];

			/* SuperTypes to fusion */
			if (isset($this->content[$contentName]['superTypes'])) {
				$superTypes = $this->content[$contentName]['superTypes'];
				array_shift($superTypes);
				foreach ($superTypes as $superType => $value) {
					if (strpos('TYPO3.Neos.NodeTypes:LinkMixin', $superType) !== FALSE) {
						$params['superTypes'] .= "link.@process.convertUris = TYPO3.Neos:ConvertUris";
						break;
					}
				}
			}

			/* Child nodes fusion */
			if (isset($this->content[$contentName]['childNodes'])) {
				foreach ($this->content[$contentName]['childNodes'] as $childNode) {
					$params['childNodes'] .= trim($this->getFusionContentTemplate($childNode));
				}
			}

			/* Properties fusion */
			if (isset($this->content[$contentName]['properties'])) {
				foreach ($this->content[$contentName]['properties'] as $name => $property) {
					if ($property['type'] == 'string' && isset($property['ui']['inspector']['editor']) && $property['ui']['inspector']['editor'] == 'TYPO3.Neos/Inspector/Editors/LinkEditor') {
						$params['properties'] .= "\n\t" . $name .'.@process.convertUris = TYPO3.Neos:ConvertUris';
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
	 * Generate html template base on generated content nodetype
	 */
	public function generateTemplate() {
		if (count($this->content)) {
			$contentName = key($this->content);
			/* Prepare params to replace html template contents */
			$arrSiteKeys = explode(':', $contentName);
			$params = [
				'imageNameSpace' => '',
				'properties' => '',
				'superTypes' => ''
			];

			/* SuperTypes to template */
			if (isset($this->content[$contentName]['superTypes'])) {
				$this->generateSuperTypesToTemplate($this->content[$contentName]['superTypes'], $params);
			}

			/* Assign child nodes to template */
			if (isset($this->content[$contentName]['childNodes'])) {
				$content = "";
				foreach ($this->content[$contentName]['childNodes'] as $name => $childNode) {
					$content .= "\n\t{" . lcfirst($name) . " -> f:format.raw()}";
				}
				$params['childNodes'] .= $content;
			}

			/* Display all properties of configuration to template */
			if (isset($this->content[$contentName]['properties'])) {
				$this->generatePropertiesToTemplate($this->content[$contentName]['properties'], $params);
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
	public function generateContentNodeType($data) {
		$this->generateConfig($data);
		$this->generateFusion();
		$this->generateTemplate();
	}

	/**
	 * @param $childNode
	 *
	 * @return string
	 */
	public function getFusionContentTemplate($childNode) {
		$name = lcfirst(key($childNode));
		return "
			$name = ContentCollection {
				nodePath = '$name'
				content.iterationName = '" . $name . "Iteration'
				attributes.class = '" . strtolower($name) . "'
			}
		";
	}
}
