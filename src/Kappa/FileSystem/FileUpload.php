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
 * Class FileUpload
 * @package Kappa\FileSystem
 */
class FileUpload extends \Nette\Http\FileUpload
{
	/**
	 * @param string $dest
	 * @return File
	 */
	public function move($dest)
	{
		parent::move($dest);

		return File::open($dest);
	}
}