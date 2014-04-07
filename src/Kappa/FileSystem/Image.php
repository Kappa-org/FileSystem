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

/**
 * Class Image
 * @package Kappa\FileSystem
 */
class Image extends \Nette\Utils\Image
{
	/**
	 * @param string|null $file
	 * @param string|null $quality
	 * @param int|null $type
	 * @return File
	 * @throws IOException
	 */
	public function save($file = NULL, $quality = NULL, $type = NULL)
	{
		if (!parent::save($file, $quality, $type)) {
			throw new IOException("Image '{$file}' has not been saved");
		}

		return File::open($file);
	}
}