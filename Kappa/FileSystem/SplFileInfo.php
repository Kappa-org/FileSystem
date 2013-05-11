<?php
/**
 * SplFileInfo.php
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 10.5.13
 *
 * @package Kappa\FileSystem
 */

namespace Kappa\FileSystem;

/**
 * Class SplFileInfo
 *
 * @package Kappa\FileSystem
 */
class SplFileInfo extends \SplFileInfo
{
	/**
	 * @param string $file
	 */
	public function __construct($file)
	{
		parent::__construct($file);
	}

	/**
	 * @param string $root
	 * @return string
	 */
	public function getRelativePath($root)
	{
		$root = realpath($root);
		return trim(str_replace($root, "", $this->getPathname()));
	}

	/**
	 * @return string
	 */
	public function getFileExtension()
	{
		return strrchr($this->getPathname(), '.');
	}

	/**
	 * @return bool
	 */
	public function isImage()
	{
		$types = array(
			'image/bmp',
			'image/gif',
			'image/jpeg',
			'image/png',
		);
		$imageInfo = @getimagesize($this->getPathname());
		if(in_array($imageInfo['mime'], $types)) {
			return true;
		} else {
			return false;
		}
	}
}
