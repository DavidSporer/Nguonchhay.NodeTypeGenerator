<?php
namespace Nguonchhay\NodeTypeGenerator\Domain\Model;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/

use Nguonchhay\NodeTypeGenerator\Service\FileService;
use TYPO3\Flow\Annotations as Flow;
use Nguonchhay\NodeTypeGenerator\Service\TemplateService;
use TYPO3\Flow\Configuration\Source\YamlSource;

abstract class AbstractNodeType {

	const PREFIX_NODETYPE_FILENAME = 'NodeTypes';
	const BASE_PATH = 'resource://Nguonchhay.NodeTypeGenerator/Private/StaticTemplates';
	const TEMP_PATH = 'Temporary';
	const NODETYPE_CONFIG_EXTENSION = '.yaml';
	const NODETYPE_FUSION_EXTENSION = '.ts2';
	const NODETYPE_TEMPLATE_EXTENSION = '.html';

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $config;

	/**
	 * @var string
	 */
	protected $fusion;

	/**
	 * @var string
	 */
	protected $template;

	/**
	 * @var array
	 */
	protected $content;

	/**
	 * @Flow\Inject
	 * @var TemplateService
	 */
	protected $templateService;

	/**
	 * @Flow\Inject
	 * @var YamlSource
	 */
	protected $yamlSource;


	/**
	 * Define the abstract method for sub class to override
	 */
	public abstract function generateConfig($data);
	public abstract function getConfigFilename($name);
	public abstract function generateFusion();
	public abstract function generateTemplate();

	/**
	 * @param string $type
	 */
	public function __construct($type = 'content') {
		$this->type = $type;
		$this->assignTemplate();
	}

	/**
	 * @return void
	 */
	public function assignTemplate() {
		$filename = ucfirst($this->type);
		$extensions = [
			'config' => $filename . self::NODETYPE_CONFIG_EXTENSION,
			'fusion' => $filename . self::NODETYPE_FUSION_EXTENSION,
			'template' => $filename. self::NODETYPE_TEMPLATE_EXTENSION
		];
		foreach ($extensions as $field => $extension) {
			$this->{$field} = self::BASE_PATH . '/' . $filename . '/' . $extension;
		}
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param string $config
	 */
	public function setConfig($config) {
		$this->config = $config;
	}

	/**
	 * @return string
	 */
	public function getFusion() {
		return $this->fusion;
	}

	/**
	 * @param string $fusion
	 */
	public function setFusion($fusion) {
		$this->fusion = $fusion;
	}

	/**
	 * @return string
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @param string $template
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}

	/**
	 * @return array
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param array $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getTemporaryPath() {
		return self::BASE_PATH . '/' . self::TEMP_PATH;
	}

	public function getFullTemporaryPath() {
		return FLOW_PATH_PACKAGES . 'Application/Nguonchhay.NodeTypeGenerator/Resources/Private/StaticTemplates/Temporary';
	}

	/**
	 * Delete all generated files (.yaml, .ts2, .html)
	 */
	public function clearGenerateFiles() {
		$files = glob($this->getFullTemporaryPath() . '/*');
		foreach($files as $file) {
			if(is_file($file)) {
				FileService::delete($file);
			}
		}
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function getFusionFilename($name) {
		return ucwords($name) . self::NODETYPE_FUSION_EXTENSION;
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function getTemplateFilename($name) {
		return ucwords($name) . self::NODETYPE_TEMPLATE_EXTENSION;
	}

	/**
	 * @param array $superTypes
	 * @param array $params
	 * @param boolean $isDocument
	 */
	public function generateSuperTypesToTemplate($superTypes, &$params, $isDocument = false) {
		array_shift($superTypes);
		foreach ($superTypes as $superType => $value) {
			if (strpos('TYPO3.Neos.NodeTypes:TitleMixin', $superType) !== FALSE) {
				if ($isDocument) {
					$params['superTypes'] .= "\n\t\t\t<neos:contentElement.wrap>\n\t\t\t\t<div>\n\t\t\t\t\t{neos:contentElement.editable(property: 'title')}\n\t\t\t\t</div>\n\t\t\t</neos:contentElement.wrap>";
				} else {
					$params['superTypes'] .= "\n\t<div{attributes -> f:format.raw()}>\n\t\t{neos:contentElement.editable(property: 'title')}\n\t</div>";
				}
			} else if (strpos('TYPO3.Neos.NodeTypes:TextMixin', $superType) !== FALSE) {
				if ($isDocument) {
					$params['superTypes'] .= "\n\t\t\t<neos:contentElement.wrap>\n\t\t\t\t<div>\n\t\t\t\t\t{neos:contentElement.editable(property: 'text')}\n\t\t\t\t</div>\n\t\t\t</neos:contentElement.wrap>";
				} else {
					$params['superTypes'] .= "\n\t<div{attributes -> f:format.raw()}>\n\t\t{neos:contentElement.editable(property: 'text')}\n\t</div>";
				}
			} else if (strpos('TYPO3.Neos.NodeTypes:ImageMixin', $superType) !== FALSE) {
				$params['superTypes'] .= "\n\t" . '<f:if condition="{image}">' . "\n\t\t\t\t" . '<media:image asset="{image}" alt="{alternativeText}" title="{title}" width="{width}" maximumWidth="{maximumWidth}" height="{height}" maximumHeight="{maximumHeight}" allowUpScaling="{allowUpScaling}" allowCropping="{allowCropping}" />' . "\n\t\t\t</f:if>";
				$params['imageNameSpace'] .= '{namespace media=TYPO3\Media\ViewHelpers}';
			} else if (strpos('TYPO3.Neos.NodeTypes:LinkMixin', $superType) !== FALSE) {
				$params['properties'] .= "\n\t<a href=\"{link -> f:format.raw()}\">{link -> f:format.raw()}</a>";
			} else if (strpos('TYPO3.Neos.NodeTypes:ContentReferences', $superType) !== FALSE || strpos('TYPO3.Neos.NodeTypes:AssetList', $superType) !== FALSE) {
				$params['superTypes'] .= "\n\t<!--Add your fusion here-->";
			}
		}
	}

	/**
	 * @param array $properties
	 * @param array $params
	 * @param boolean $isDocument
	 */
	public function generatePropertiesToTemplate($properties, &$params, $isDocument = false) {
		foreach ($properties as $name => $property) {
			if ($name != 'layout') {
				$type = $property['type'];
				if ($type == 'integer') {
					$params['properties'] .= "\n\t<div>{" . $name . "}</div>";
				} else if ($type == 'string') {
					if (isset($property['ui']['inlineEditable'])) {
						if ($isDocument) {
							$params['properties'] .= "\n\t\t\t<neos:contentElement.wrap>\n\t\t\t\t<div>\n\t\t\t\t\t{neos:contentElement.editable(property: '$name')}\n\t\t\t\t</div>\n\t\t\t</neos:contentElement.wrap>";
						} else {
							$params['properties'] .= "\n\t<div{attributes -> f:format.raw()}>\n\t\t{neos:contentElement.editable(property: '$name')}\n\t</div>";
						}
					} else if (isset($property['ui']['inspector']['editor']) && $property['ui']['inspector']['editor'] == 'TYPO3.Neos/Inspector/Editors/LinkEditor') {
						$params['properties'] .= "\n\t\t\t" . '<a href="{' . $name . '-> f:format.raw()}">{' . $name . " -> f:format.raw()}</a>";
					} else {
						$params['properties'] .= "\n\t\t\t{" . $name . " -> f:format.raw()}";
					}
				} else if ($type == 'DateTime') {
					$params['properties'] .= "\n\t\t\t" . '<f:if condition="{' . $name . '}"><f:format.date format="' . $property['ui']['inspector']['editorOptions']['format'] . '">{' . $name . ' -> f:format.raw()}</f:format.date></f:if>';
				} else if ($type == 'TYPO3\Media\Domain\Model\ImageInterface') {
					$params['properties'] .= "\n\t\t\t" . '<f:if condition="{' . $name . '}">' . "\t\t\t\t" . '<media:image asset="{' . $name . '}" alt="{alternativeText}" title="{title}" width="{width}" maximumWidth="{maximumWidth}" height="{height}" maximumHeight="{maximumHeight}" allowUpScaling="{allowUpScaling}" allowCropping="{allowCropping}" />' . "\t\t\t" . '</f:if>';
				} else if ($type == 'reference' || $type == 'references') {
					$params['properties'] .= "\n\t\t\t<!-- Add template for $name reference(s) -->";
				} else if ($type == 'TYPO3\Media\Domain\Model\Asset' || $type == 'array<TYPO3\Media\Domain\Model\Asset>') {
					$params['properties'] .= "\n\t\t\t<!-- Add template for $name asset(s)-->";
				}
			}
		}
	}
}
