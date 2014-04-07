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
 * Class File
 * @package Kappa\FileSystem
 */
class File
{
	/** @var string */
	private $path;

	/**
	 * @param string $path
	 */
	private function __construct($path)
	{
		$this->path = realpath($path);
	}

	/**
	 * @param string $path
	 * @param string|null $content
	 * @return File
	 * @throws IOException
	 */
	public static function create($path, $content = null)
	{
		if (is_file($path)) {
			throw new FileAlreadyExistException("File '{$path}' has not been created because already exist");
		}
		if (@file_put_contents($path, $content) === false) {
			throw new IOException("File '{$path}' has not been created");
		}

		return new self($path);
	}

	/**
	 * @param string $path
	 * @return File
	 * @throws FileNotFoundException
	 */
	public static function open($path)
	{
		if (!is_file($path)) {
			throw new FileNotFoundException("File '{$path}' has not been found");
		}

		return new self($path);
	}
} 