# MemoryFile

PHP Memories (Photos and Movies) storage manager. Currently unstable.

#### Directory Structure

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

#### Sample Usage

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
