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
 * Class FileSystem
 * @package Kappa\FileSystem
 */
class FileSystem
{
	/**
	 * @param File|Directory $object
	 * @throws InvalidArgumentException
	 */
	public static function remove($object)
	{
		if (!$object instanceof File && !$object instanceof Directory) {
			throw new InvalidArgumentException(__METHOD__ . ": Argument must be instance of File or Directory");
		}
		\Nette\Utils\FileSystem::delete($object->getInfo()->getPathname());
	}

	/**
	 * @param File|Directory $object
	 * @param string $newName
	 * @param bool $overwrite
	 * @return Directory|File
	 * @throws InvalidArgumentException
	 */
	public static function rename($object, $newName, $overwrite = true)
	{
		if (!$object instanceof File && !$object instanceof Directory) {
			throw new InvalidArgumentException(__METHOD__ . ": Argument must be instance of File or Directory");
		}
		$newName = $object->getInfo()->getPath() . DIRECTORY_SEPARATOR . $newName;
		\Nette\Utils\FileSystem::rename($object->getInfo()->getPathname(), $newName, $overwrite);
		if ($object instanceof File) {
			return File::open($newName);
		} else {
			return Directory::open($newName);
		}
	}

	/**
	 * @param File|Directory $source
	 * @param string|Directory $target
	 * @param bool $overwrite
	 * @return Directory|File
	 * @throws InvalidArgumentException
	 */
	public static function copy($source, $target, $overwrite = true)
	{
		if (!$source instanceof File && !$source instanceof Directory) {
			throw new InvalidArgumentException(__METHOD__ . ": Argument must be instance of File or Directory");
		}
		if (!is_string($target) && !$target instanceof Directory) {
			throw new InvalidArgumentException(__METHOD__ . ": Target must be string or instance of Directory");
		}
		if ($source === $target) {
			throw new InvalidArgumentException(__METHOD__ . ": Target must not be same as source");
		}
		if ($target instanceof Directory) {
			$target = $target->getInfo()->getPathname() . DIRECTORY_SEPARATOR . $source->getInfo()->getBasename();
		}
		\Nette\Utils\FileSystem::copy($source->getInfo()->getPathname(), $target, $overwrite);
		if ($source instanceof File) {
			return File::open($target);
		} else {
			return Directory::open($target);
		}
	}

	/**
	 * @param File|Directory $source
	 * @param string|Directory $target
	 * @param bool $overwrite
	 * @return Directory|File
	 */
	public static function move($source, $target, $overwrite = true)
	{
		$result = self::copy($source, $target, $overwrite);
		FileSystem::remove($source);

		return $result;
	}
} 