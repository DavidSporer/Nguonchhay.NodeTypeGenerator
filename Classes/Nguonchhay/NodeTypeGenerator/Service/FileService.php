<?php
namespace Nguonchhay\NodeTypeGenerator\Service;

/***************************************************************************
 * This file is part of the Nguonchhay.NodeTyoeGenerator package.          *
 **************************************************************************/

use TYPO3\Flow\Annotations as Flow;

class FileService {




	/**
	 * @param $filenameAndPath
	 *
	 * @return string
	 */
	public static function read($filenameAndPath) {
		if (file_exists($filenameAndPath)) {
			return file_get_contents($filenameAndPath);
		}
		return '';
	}

	/**
	 * @param $filenameAndPath
	 * @param $content
	 */
	public static function write($filenameAndPath, $content) {
		file_put_contents($filenameAndPath, $content);
	}

	/**
	 * @param $filenameAndPath
	 */
	public static function delete($filenameAndPath) {
		if (file_exists($filenameAndPath)) {
			unlink($filenameAndPath);
		}
	}
}
