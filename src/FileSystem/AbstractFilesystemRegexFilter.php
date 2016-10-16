<?php
namespace MemoryFile\FileSystem;

/**
 * Original class takes from the following lib:
 * https://github.com/thiagodp/php-util/blob/master/io
 *
 * Original comments included below.
 */

/** THIS FILE:
 * a)   BELONGS TO THE 'PHP-UTIL' LIBRARY:
 *      https://github.com/thiagodp/php-util
 *
 * b)   IS DISTRIBUTED UNDER THE CREATIVE COMMONS LICENCE (CC BY 3.0):
 *      http://creativecommons.org/licenses/by/3.0/
 *
 * USE IT AT YOUR OWN RISK!
 */

/**
 * A regex-based filter for files and directories.
 *
 * @author  Thiago Delgado Pinto
 * @version 1.0
 */
abstract class AbstractFilesystemRegexFilter extends \RecursiveRegexIterator
{
    protected $regex;

    public function __construct(\RecursiveIterator $it, $regex)
    {
        $this->regex = $regex;
        parent::__construct($it, $regex);
    }
}

/* End of file */
