<?php
/**
 * This file is part of the Kappa package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
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
		parent::__construct(realpath($file));
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
		if (in_array($imageInfo['mime'], $types)) {
			return true;
		} else {
			return false;
		}
	}
}
