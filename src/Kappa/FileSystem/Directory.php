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
 * Class Directory
 * @package Kappa\FileSystem
 */
class Directory 
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
	 * @return Directory
	 * @throws DirectoryNotFoundException
	 */
	public static function open($path)
	{
		if (!is_dir($path)) {
			throw new DirectoryNotFoundException("Directory '{$path}' has not been found");
		}

		return new self($path);
	}
} 