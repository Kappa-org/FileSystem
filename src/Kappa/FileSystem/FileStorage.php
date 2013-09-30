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
	protected function getPath()
	{
		return $this->path;
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

	/**
	 * @param string $newName
	 * @param bool $overwrite
	 * @return bool|File|Directory
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function rename($newName, $overwrite = false)
	{
		if ($this->isCreated()) {
			if (!is_string($newName)) {
				throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($newName) . " given");
			}
			$newPath = $this->getInfo()->getPath() . DIRECTORY_SEPARATOR . $newName;
			if ($this instanceof File) {
				if (is_file($newPath) && !$overwrite) {
					throw new IOException("Failed to rename to '$newPath', because file $newPath already exist");
				}
			}
			if ($this instanceof Directory) {
				if (is_dir($newPath) && !$overwrite) {
					throw new IOException("Failed to rename to '$newPath', because file $newPath already exist");
				} else {
					if (is_dir($newPath)) {
						$directory = new Directory($newPath);
						$directory->remove();
					}
				}
			}
			if (@rename($this->path, $newPath) === true) {
				$this->path = $newPath;

				return true;
			} else {
				return false;
			}
		} else {
			throw new IOException("Directory {$this->path} must be firstly created");
		}
	}
}