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
 * Class Directory
 * @package Kappa\FileSystem
 */
class Directory extends FileSystem
{	/**
	 * @return array
	 * @throws IOException
	 */
	public function getContent()
	{
		if($this->isCreated()) {
			$it = iterator_to_array(new \FilesystemIterator($this->path));
			/** @var \SplFileInfo $file */
			foreach($it as $path => $file) {
				if($file->isFile()) {
					$output[$path] = new File($file->getPathname());
				}
				if($file->isDir()) {
					$output[$path] = new Directory($file->getPathname());
				}
			}
			return (isset($output)) ? $output : array();
		} else {
			throw new IOException("Directory {$this->path} must be firstly created");
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
	 * @param array $ignore
	 * @param bool $returnNew
	 * @param bool $overwrite
	 * @return Directory
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function copy($target, $returnNew = true, $overwrite = false, array $ignore = array())
	{
		if (!is_string($target)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($target) . " given");
		}
		if (file_exists($target) && !$overwrite) {
			throw new IOException("Failed to copy directory '$target'");
		} else {
			$dir = new Directory($target);
			$dir->create();
			$this->_copy($this, $dir->getInfo()->getPathname(), $ignore);
			return ($returnNew) ? new Directory($dir->getInfo()->getPathname(), Directory::INTUITIVE) : true;
		}
	}

	/**
	 * @param string $target
	 * @param bool $overwrite
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function move($target, $overwrite = false)
	{
		if (!is_string($target)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($target) . " given");
		}
		if (!is_bool($overwrite)) {
			throw new InvalidArgumentException(__METHOD__ . " Second argument must to be bool, " . gettype($overwrite) . " given");
		}
		if (file_exists($target) && !$overwrite) {
			throw new IOException("Failed to copy directory '$target'");
		} else {
			$directory = $this->copy($target, true, $overwrite, array());
			if($this->remove() === true && $directory->isCreated()) {
				$this->path = realpath($directory->getInfo()->getPathname());
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * @param Directory $directory
	 * @param string $copyDir
	 * @param array $ignore
	 */
	private function _copy(Directory $directory, $copyDir, array $ignore = array())
	{
		/** @var $obj \Kappa\FileSystem\File|\Kappa\FileSystem\Directory */
		foreach($directory->getContent() as $path => $obj) {
			if($obj instanceof File) {
				if(!in_array($obj->getInfo()->getBasename(), $ignore)) {
					@copy($path, $copyDir . DIRECTORY_SEPARATOR . $obj->getInfo()->getBasename());
				}
			}
			if($obj instanceof Directory) {
				if(!in_array($obj->getInfo()->getBasename(), $ignore)) {
					$newCopy = new Directory($copyDir . DIRECTORY_SEPARATOR . $obj->getInfo()->getBasename());
					$newCopy->create();
					$this->_copy($obj, $newCopy->getInfo()->getPathname());
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
		if($this->isCreated()) {
			$it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path), \RecursiveIteratorIterator::CHILD_FIRST);
			/** @var \SplFileInfo $file */
			foreach ($it as $file) {
				if (in_array($file->getBasename(), array('.', '..'))) {
					continue;
				} elseif ($file->isDir()) {
					if (true !== @rmdir($file->getPathname())) {
						throw new IOException("Failed to remove directory '$this->path'");
					}
				} elseif ($file->isFile() || $file->isLink()) {
					if (true !== @unlink($file->getPathname())) {
						throw new IOException("Failed to remove directory '$this->path'");
					}
				}
			}
			@rmdir($this->path);
			return !$this->isCreated();
		} else {
			throw new IOException("Directory {$this->path} must be firstly created");
		}
	}
}
