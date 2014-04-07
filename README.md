# Kappa\FileSystem [![Build Status](https://travis-ci.org/Kappa-app/FileSystem.png?branch=master)](https://travis-ci.org/Kappa-app/FileSystem)

Easy system for work with files and directories

## Requirements:

* PHP 5.3.3 or higher
* [Kappa\Utils](https://github.com/kappa-org/utils)
* [Nette\Finder](https://github.com/nette/finder) 2.2.*
* [Nette\Http](https://github.com/nette/http) 2.2.* *for FileUpload*
* [Nette\Utils](https://github.com/nette/utils) 2.2.* *for Image*

## Installation

The best way to install Kappa/FileSystem is using Composer:

```bash
$ composer require kappa/filesystem:@dev
```

## Usages

Create a new file or directory:
```php
$file = File::create('path/to/file.txt', 'Hello wolrd!') // Create a new file with Hello world! text
$directory  = Directory::create('path/to/directory') // Create a new directory
```

Opening files and directories:
```php
$file = File::open('path/to/file'); // Open file
$dorectory = Directory::open('path/to/file'); // Open directory
```

### File API:

* ```read()``` - Returns file content
* ```overwrite(content)``` - Overwrite file content *(content can be null for clear file)*
* ```clear()``` - Clear file content *(same as ```overwrite(null)```)*
* ```append(content, newLine = true)``` - Append text to end file
* ```getInfo()``` - Returns SplFileInfo

### Directory API:

* ```getInfo()``` - Returns SplFileInfo
* ```getDirectories()``` - Returns directories in directory as array ```path => SplFileInfo```
* ```getFiles()``` - Returns files in directory as array ```path => SplFileInfo```
* ```getContent()``` - Returns directories and files in directory as array ```path => SplFileInfo```

### FileSystem API:

* ```remove(source)``` - Remove file or directory, ```source``` must be instance of File or Directory
* ```rename(source, new name, overwrite)``` - Renamed file or directory. ```source``` must be instance of File or
Directory and return new instance of object
* ```copy(source, target, overwrite)``` - Copy source to target, ```source``` must be instance of File or Directory,
```target``` can be string or instance of Directory. Return instance of copy file;
* ```move(source, target, overwrite)``` - Same as ```copy()``` but remove source after copy

### Examples:

```php
$file = File::create('file.txt');
$file = FileSystem::rename($file, 'superFile.txt');
$file->getInfo()->getBasename(); // Return superFile.txt
```