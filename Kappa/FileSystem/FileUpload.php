<?php
/**
 * FileUpload.php
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 9.5.13
 *
 * @package Kappa\FileSystem
 */

namespace Kappa\FileSystem;

/**
 * Class FileUpload
 *
 * @package Kappa\FileSystem
 */
class FileUpload extends File
{
	/**
	 * @param \Nette\Http\FileUpload $file
	 * @param string $target
	 * @throws IOException
	 */
	public function __construct(\Nette\Http\FileUpload $file, $target)
	{
		if(!$file->isOk()) {
			throw new IOException("Failed to upload file" . $file->getName());
		}
		$file->move($target);
		return parent::__construct($target);
	}
}
