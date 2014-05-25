# Kappa\FileSystem [![Build Status](https://travis-ci.org/Kappa-app/FileSystem.png?branch=master)](https://travis-ci.org/Kappa-app/FileSystem)

Easy system for work with files and directories

## Requirements:

* PHP 5.3.3 or higher
* [Kappa\Utils](https://github.com/kappa-org/utils) dev-nette-2.1
* [Nette framework](https://github.com/nette/nette) 2.1.*

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

Upload files via [FileUpload](https://github.com/nette/http/blob/master/src/Http/FileUpload.php):
```php
// $fileUpload is instance of FileUpload from forms example...
$file = File::upload($fileUpload, 'path/to/save/file');
```

Load file from [Nette\Image](https://github.com/nette/utils/blob/master/src/Utils/Image.php)
```php
$image = Image::fromFile('image.png');
$image->resize(10,10);
$file = File::fromImage($image, 'newImage.png');
```
Create a new image 'newImage' with dimensions 10x10 px and return instance of File

*If you can work with same image without creating new file use original file name as second parameter*

### File API:

* ```read()``` - Returns file content
* ```overwrite(content)``` - Overwrite file content *(content can be null for clear file)*
* ```clear()``` - Clear file content *(same as ```overwrite(null)```)*
* ```append(content, newLine = true)``` - Append text to end file
* ```getInfo()``` - Returns SplFileInfo
* ```toImage()``` - Returns Nette\Utils\Image

### Directory API:

* ```getInfo()``` - Returns SplFileInfo
* ```getDirectories()``` - Returns directories in directory as array ```path => Directory```
* ```getFiles()``` - Returns files in directory as array ```path => File```
* ```getContent()``` - Returns directories and files in directory as array ```path => Directory|File```

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