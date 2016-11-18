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
