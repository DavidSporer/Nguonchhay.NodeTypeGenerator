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
	public abstract function getFusionFilename($name);

	public abstract function generateTemplate();
	public abstract function getTemplateFilename($name);

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

	public function generateHtmlBaseOnProperties($properties, $param) {

	}
}
