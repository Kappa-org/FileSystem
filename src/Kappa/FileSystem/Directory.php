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
 * Class Directory
 * @package Kappa\FileSystem
 */
class Directory extends FileStorage
{
	/**
	 * @param string $path
	 * @throws InvalidArgumentException
	 * @throws DirectoryNotFoundException
	 * @throws IOException
	 * @throws DirectoryAlreadyExistException
	 */
	public function __construct($path)
	{
		if (!is_string($path)) {
			throw new InvalidArgumentException("Path must be string, " . gettype($path) . " given");
		}
		if (!is_dir($path)) {
			if (!$this->create($path)) {
				throw new IOException("Unable to create directory '{$path}'");
			}
		}
		$this->setPath($path);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	private function create($path)
	{
		return @mkdir($path, 0777);
	}

	/**
	 * @param string $newName
	 * @param bool $overwrite
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws IOException
	 * @throws DirectoryAlreadyExistException
	 */
	public function rename($newName, $overwrite = false)
	{
		if ($this->isCreated()) {
			if (!is_string($newName)) {
				throw new InvalidArgumentException("Name must to be string, " . gettype($newName) . " given");
			}
			$newPath = $this->getInfo()->getPath() . DIRECTORY_SEPARATOR . $newName;
			if (is_dir($newPath) && !$overwrite) {
				throw new DirectoryAlreadyExistException("Directory '{$newPath}' already exist");
			} else {
				if (is_dir($newPath)) {
					$directory = new Directory($newPath, Directory::LOAD);
					$directory->remove();
				}
			}
			if (@rename($this->getPath(), $newPath)) {
				$this->setPath($newPath);

				return true;
			} else {
				return false;
			}
		} else {
			throw new IOException("Directory {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @return array
	 * @throws IOException
	 */
	public function getContent()
	{
		if ($this->isCreated()) {
			$it = iterator_to_array(new \FilesystemIterator($this->getPath()));
			/** @var \SplFileInfo $file */
			foreach ($it as $path => $file) {
				if ($file->isFile()) {
					$output[$path] = new File($file->getPathname());
				}
				if ($file->isDir()) {
					$output[$path] = new Directory($file->getPathname());
				}
			}

			return (isset($output)) ? $output : array();
		} else {
			throw new IOException("Directory {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @return array
	 */
	public function getFiles()
	{
		$content = $this->getContent();
		/** @var File|Directory $file */
		foreach ($content as $path => $file) {
			if ($file instanceof File) {
				$output[$path] = $file;
			}
		}

		return (isset($output)) ? $output : array();
	}

	/**
	 * @return array
	 */
	public function getDirectories()
	{
		$content = $this->getContent();
		/** @var File|Directory $file */
		foreach ($content as $path => $dir) {
			if ($dir instanceof Directory) {
				$output[$path] = $dir;
			}
		}

		return (isset($output)) ? $output : array();
	}

	/**
	 * @param string $target
	 * @param bool $returnNew
	 * @param bool $overwrite
	 * @param array $ignore
	 * @return bool|Directory
	 * @throws InvalidArgumentException
	 * @throws IOException
	 * @throws DirectoryAlreadyExistException
	 */
	public function copy($target, $returnNew = true, $overwrite = false, array $ignore = array())
	{
		if ($this->isCreated()) {
			if (!is_string($target)) {
				throw new InvalidArgumentException("Target must to be string, " . gettype($target) . " given");
			}
			if (file_exists($target) && !$overwrite) {
				throw new DirectoryAlreadyExistException("Directory '{$this->getPath()}' already exist");
			} else {
				if (file_exists($target)) {
					$deleteDir = new Directory($target);
					$deleteDir->remove();
				}
				$dir = new Directory($target);
				$this->doCopy($this, $dir->getPath(), $ignore);

				return ($returnNew) ? new Directory($dir->getPath()) : true;
			}
		} else {
			throw new IOException("Directory {$this->getPath()} must be firstly created");
		}
	}

	/**
	 * @param string $target
	 * @param bool $overwrite
	 * @param array $ignore
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws IOException
	 * @throws DirectoryAlreadyExistException
	 */
	public function move($target, $overwrite = false, array $ignore = array())
	{
		if (!is_string($target)) {
			throw new InvalidArgumentException("Target must to be string, " . gettype($target) . " given");
		}
		if (file_exists($target) && !$overwrite) {
			throw new DirectoryAlreadyExistException("Directory '{$target}' already exist");
		} else {
			$directory = $this->copy($target, true, $overwrite, $ignore);
			if ($directory) {
				if ($this->remove()) {
					$this->setPath(realpath($directory->getPath()));

					return true;
				} else {
					throw new IOException("Directory {$this->getPath()} has not been removed");
				}
			} else {
				throw new IOException("Unable to copy directory to '{$target}'");
			}
		}
	}

	/**
	 * @param Directory $directory
	 * @param string $copyDir
	 * @param array $ignore
	 */
	private function doCopy(Directory $directory, $copyDir, array $ignore = array())
	{
		/** @var $obj \Kappa\FileSystem\File|\Kappa\FileSystem\Directory */
		foreach ($directory->getContent() as $path => $obj) {
			if ($obj instanceof File) {
				if (!in_array($obj->getInfo()->getBasename(), $ignore)) {
					@copy($path, $copyDir . DIRECTORY_SEPARATOR . $obj->getBaseName());
				}
			}
			if ($obj instanceof Directory) {
				if (!in_array($obj->getInfo()->getBasename(), $ignore)) {
					$newCopy = new Directory($copyDir . DIRECTORY_SEPARATOR . $obj->getBaseName());
					$this->doCopy($obj, $newCopy->getPath());
				}
			}
		}
	}

	/**
	 * @return bool
	 * @throws IOException
	 */
	public function remove()
	{
		if ($this->isCreated()) {
			$it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->getPath()), \RecursiveIteratorIterator::CHILD_FIRST);
			/** @var \SplFileInfo $file */
			foreach ($it as $file) {
				if (in_array($file->getBasename(), array('.', '..'))) {
					continue;
				} elseif ($file->isDir()) {
					if (true !== @rmdir($file->getPathname())) {
						throw new IOException("Failed to remove directory '$this->getPath()'");
					}
				} elseif ($file->isFile() || $file->isLink()) {
					if (true !== @unlink($file->getPathname())) {
						throw new IOException("Failed to remove directory '$this->getPath()'");
					}
				}
			}
			if (@rmdir($this->getPath())) {
				return !$this->isCreated();
			} else {
				return false;
			}
		} else {
			throw new IOException("Directory {$this->getPath()} must be firstly created");
		}
	}
}
