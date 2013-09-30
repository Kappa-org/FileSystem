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

/**
 * Class FileUpload
 * @package Kappa\FileSystem
 */
class FileUpload extends File
{
	/**
	 * @param \Nette\Http\FileUpload $file
	 * @param int $target
	 * @param bool $overwrite
	 * @throws IOException
	 * @throws FileAlreadyExistException
	 */
	public function __construct(\Nette\Http\FileUpload $file, $target, $overwrite = true)
	{
		if (!$file->isOk()) {
			throw new IOException("Failed to upload file {$file->getName()}");
		}
		if (is_file($target) && !$overwrite) {
			throw new FileAlreadyExistException("File {$target} already exist");
		}
		$file->move($target);
		$this->setPath($target);
	}
}