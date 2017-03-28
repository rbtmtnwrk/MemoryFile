# MemoryFile

PHP Memories (Photos and Movies) storage manager. Currently unstable.

### Directory Structure

MemoryFile reads an image file's info, and places them in the following directory structure:

```
[REPOSITORY]
    |
    | - [YEAR]
    |       |
    .       | - [MONTH]
            |       |
            |       | - [MOVIES]
            |       | - [SOURCE / CAMERA]
            |       | - [NO_SOURCE]
            |       |
            .       .

```

### File Naming

Files imported will be prefixed with the day, hour, and second of the file's redable creation date:

|File Name|Creation Date|MemoryFile Name|
|--|--|--|
|IMG_0001.JPG|3/01/2017 2:11 PM|011411_IMG_0001.JPG|

If a file has a duplicate name, but has different file contents, and is created at the same time, it will be pre-pended with an MF_X_ prefix:

|Duplicate File Name|Creation Date|MemoryFile Name|
|--|--|--|
|IMG_0001.JPG|3/01/2017 2:11 PM|MF_1_011411_IMG_0001.JPG|

The number will increment with each duplicate file so the next file name would be MF_2_011411_IMG_0001.JPG.

### Sample Usage

Using composer.

```
include 'MemoryFile/vendor/autoload.php';

// Also set your timezone:
date_default_timezone_set('America/New_York');
```

Import photos and movies.

```
. . .

// Store files here
$service = \MemoryFile\Service::create('/memories');

// Import files from here
$service->import('/photos_and_movies');
```

Transform a specific file. Good for inspecting it's EXIF information.

```
. . .

$service = \MemoryFile\Service::create('/memories');

$memoryfile = $service->transformFile('/photos_and_movies/IMG_0001.JPG');

var_dump(print_r($memoryfile, true));
```
