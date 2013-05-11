<?php
/**
 * exceptions.php
 *
 * @author Ondřej Záruba <zarubaondra@gmail.com>
 * @date 1.5.13
 *
 * @package Kappa\FileSystem
 */

namespace Kappa\FileSystem;

/**
 * Class InvalidArgumentException
 *
 * @package Kappa\FileSystem
 */
class InvalidArgumentException extends \LogicException
{

}

/**
 * Class IOException
 *
 * @package Kappa\FileSystem
 */
class IOException extends \RuntimeException
{

}