<?php
/**
 * This file is part of the Kappa\FileSystem package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Kappa\FileSystem;

use Kappa\Utils\Validators;

/**
 * Class SplFileInfo
 * @package Kappa\FileSystem
 */
class SplFileInfo extends \SplFileInfo
{
	/**
	 * @return bool
	 */
	public function isImage()
	{
		return Validators::isImage($this->getPathname());
	}

	/**
	 * @param string $root
	 * @return string
	 */
	public function getRelativePath($root = null)
	{
		$root = realpath(($root) ? : $_SERVER['DOCUMENT_ROOT']);

		return str_replace($root, null, $this->getPathname());
	}
} 