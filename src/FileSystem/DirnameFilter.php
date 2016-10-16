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
 * Directory name filter that uses regular expression.
 *
 * <br />
 * How to use it:
 * <code>
 * $startPath = '.'; // Current directory
 * $pattern = '/^[^\.]/i'; // "Not starting with dot"
 *
 * $dirIt = new RecursiveDirectoryIterator( $startPath );
 * $filter = new DirNameFilter( $dirIt, $pattern );
 * $itIt = new RecursiveIteratorIterator( $filter, RecursiveIteratorIterator::SELF_FIRST );
 *
 * foreach ( $itIt as $file ) {
 *      echo $file->getFilename(), ' (', $file->getRealpath(), ') <br />';
 * }
 * </code>
 *
 * @author  Thiago Delgado Pinto
 * @version 1.0
 */

class DirnameFilter extends AbstractFilesystemRegexFilter
{
    /**
     * Filter directories against the regex
     * @return string
     */
    public function accept()
    {
        return ( ! $this->isDir() || preg_match($this->regex, $this->getFilename()));
    }
}

/* End of file */
