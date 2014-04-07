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
} 