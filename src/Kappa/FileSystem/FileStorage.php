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
 * Class FileSystem
 * @package Kappa\FileSystem
 */
class FileStorage
{
	const LOAD = 0;

	const CREATE = 1;

	/** @var string */
	private $path;

	/**
	 * @param string $path
	 */
	protected function setPath($path)
	{
		$this->path = realpath($path);
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getBaseName()
	{
		return $this->getInfo()->getBasename();
	}

	/**
	 * @return bool
	 */
	protected function isCreated()
	{
		if (is_writable($this->getPath()) && is_readable($this->getPath())) {
			if ($this instanceof File) {
				return is_file($this->path);
			} elseif ($this instanceof Directory) {
				return is_dir($this->path);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * @return SplFileInfo
	 * @throws IOException
	 */
	public function getInfo()
	{
		if ($this->isCreated()) {
			return new SplFileInfo($this->path);
		} else {
			throw new IOException("Directory {$this->path} must be firstly created");
		}
	}
}