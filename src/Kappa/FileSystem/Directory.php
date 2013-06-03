<?php
/**
 * Directory.php
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 1.5.13
 *
 * @package Kappa\FileSystem
 */

namespace Kappa\FileSystem;

/**
 * Class Directory
 *
 * @package Kappa\FileSystem
 */
class Directory extends FileSystem
{
	/** @var string|null */
	private $path;

	/**
	 * @param string $path
	 * @throws InvalidArgumentException
	 */
	public function __construct($path)
	{
		if (!is_string($path)) {
			throw new InvalidArgumentException(__METHOD__ . " Argument must to be string, " . gettype($path) . " given");
		}
		$this->path = (file_exists($path)) ? realpath($path) : $this->create($path);
		parent::__construct($this->path);
	}

	/**
	 * @param string $newName
	 * @param bool $overwrite
	 * @return $this
	 * @throws InvalidArgumentException
	 * @throws IOException
	 */
	public function rename($newName, $overwrite = false)
	{
		if (!is_string($newName)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($newName) . " given");
		}
		if (!is_bool($overwrite)) {
			throw new InvalidArgumentException(__METHOD__ . " Second argument must to be bool, " . gettype($overwrite) . " given");
		}
		$newPath = $this->getInfo()->getPath() . DIRECTORY_SEPARATOR . $newName;
		if (is_dir($newPath) && !$overwrite) {
			throw new IOException("Failed to rename to '$newPath', because file $newPath already exist");
		} else {
			if(is_dir($newPath)) {
				$directory = new Directory($newPath);
				$directory->remove();
			}
			if (true === @rename($this->path, $newPath)) {
				return new Directory($newPath);
			} else {
				throw new IOException("Failed to rename from '$this->path' to '$newPath'");
			}
		}

	}

	/**
	 * @return $this
	 * @throws IOException
	 */
	public function remove()
	{
		$it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path), \RecursiveIteratorIterator::CHILD_FIRST);
		/** @var $file \SplFileInfo */
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
		if (true === @rmdir($this->path)) {
			$this->path = null;
			return $this;
		} else {
			throw new IOException("Failed to remove directory '$this->path'");
		}
	}

	/**
	 * @return array
	 */
	public function getContent()
	{
		$it = iterator_to_array(new \FilesystemIterator($this->path));
		/** @var $file \SplFileInfo */
		foreach($it as $path => $file) {
			if($file->isFile()) {
				$output[$path] = new File($file->getPathname());
			}
			if($file->isDir()) {
				$output[$path] = new Directory($file->getPathname());
			}
		}
		return (isset($output)) ? $output : array();
	}

	/**
	 * @return array
	 */
	public function getFiles()
	{
		$files = iterator_to_array(new \FilesystemIterator($this->path));
		/** @var $file \SplFileInfo */
		foreach ($files as $path => $file) {
			if ($file->isFile()) {
				$output[$path] = new File($path);
			}
		}
		return (isset($output)) ? $output : array();
	}

	/**
	 * @return array
	 */
	public function getDirectories()
	{
		$files = iterator_to_array(new \FilesystemIterator($this->path));
		/** @var $file \SplFileInfo */
		foreach ($files as $path => $file) {
			if ($file->isDir()) {
				$output[$path] = new Directory($path);
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
	public function copy($target, array $ignore = array(), $returnNew = true, $overwrite = false)
	{
		if (!is_string($target)) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be string, " . gettype($target) . " given");
		}
		if (!is_bool($returnNew)) {
			throw new InvalidArgumentException(__METHOD__ . " Second argument must to be bool, " . gettype($overwrite) . " given");
		}
		if (!is_bool($overwrite)) {
			throw new InvalidArgumentException(__METHOD__ . " Third argument must to be bool, " . gettype($overwrite) . " given");
		}
		if (file_exists($target) && !$overwrite) {
			throw new IOException("Failed to copy directory '$target'");
		} else {
			$dir = $this->create($target);
			$this->_copy($this, $dir, $ignore);
			return ($returnNew) ? new Directory($dir) : $this;
		}
	}

	/**
	 * @param string $target
	 * @param bool $overwrite
	 * @return Directory
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
			$this->copy($target, array(), true, $overwrite);
			$this->remove();
			return new Directory($target);
		}
	}

	/**
	 * @param Directory|File $object
	 * @param bool $move
	 * @param bool $returnNew
	 * @param bool $overwrite
	 * @return Directory|File
	 * @throws InvalidArgumentException
	 */
	public function append($object, $move = true, $returnNew = false, $overwrite = false)
	{
		if (!$object instanceof File && !$object instanceof Directory) {
			throw new InvalidArgumentException(__METHOD__ . " First argument must to be object File or Directory, " . gettype($object) . " given");
		}
		if (!is_bool($move)) {
			throw new InvalidArgumentException(__METHOD__ . " Second argument must to be bool, " . gettype($move) . " given");
		}
		if (!is_bool($returnNew)) {
			throw new InvalidArgumentException(__METHOD__ . " Third argument must to be bool, " . gettype($move) . " given");
		}
		if (!is_bool($overwrite)) {
			throw new InvalidArgumentException(__METHOD__ . " Fourth argument must to be bool, " . gettype($move) . " given");
		}
		$path = $this->path . DIRECTORY_SEPARATOR . $object->getInfo()->getBasename();
		if ($move) {
			$object->move($path, $overwrite);
			return ($returnNew) ? $object : $this;
		} else {
			$object->copy($path, true, $overwrite);
			return ($returnNew) ? $object : $this;
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
					copy($path, $copyDir . DIRECTORY_SEPARATOR . $obj->getInfo()->getBasename());
				}
			}
			if($obj instanceof Directory) {
				if(!in_array($obj->getInfo()->getBasename(), $ignore)) {
					$newCopy = $this->create($copyDir . DIRECTORY_SEPARATOR . $obj->getInfo()->getBasename());
					$this->_copy($obj, $newCopy);
				}
			}
		}
	}

	/**
	 * @param string $path
	 * @return string
	 * @throws IOException
	 */
	private function create($path)
	{
		if (true === @mkdir($path, 0777)) {
			return realpath($path);
		} else {
			throw new IOException("Failed to create directory '$path'");
		}
	}
}
