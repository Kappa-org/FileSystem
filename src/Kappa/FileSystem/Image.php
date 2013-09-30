<?php
/**
 * This file is part of the Kappa/FileSystem package.
 *
 * (c) Ondřej Záruba <zarubaondra@gmail.com>
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Kappa\FileSystem;

use Nette\Image as BaseImage;

/**
 * Class Image
 * @package Kappa\FileSystem
 */
class Image extends BaseImage
{
	/**
	 * @param string|null $name
	 * @param int|null $quality
	 * @param int|null $type
	 * @return File
	 * @throws IOException
	 */
	public function save($name = null, $quality = null, $type = null)
	{
		$saved = parent::save($name, $quality, $type);
		$file = new File($name, File::LOAD);
		if ($saved) {
			return $file;
		} else {
			throw new IOException("Unable to crate image '{$name}'");
		}
	}
}