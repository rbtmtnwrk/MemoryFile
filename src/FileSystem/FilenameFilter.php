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
 * File name filter that uses regular expression.
 *
 * <br />
 * How to use it:
 * <code>
 * $startPath = '.'; // Current directory
 * $pattern = '/\.php$/i'; // "Terminating with .php"
 *
 * $dirIt = new RecursiveDirectoryIterator( $startPath );
 * $filter = new FileNameFilter( $dirIt, $pattern );
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
class FilenameFilter extends AbstractFilesystemRegexFilter
{
    protected $filtered;
    protected $folders;

    public function getFiltered()
    {
        return $this->filtered;
    }

    public function getFolders()
    {
        return $this->folders;
    }

    public function __construct()
    {
        $this->filtered = [];
        $this->folders  = [];

        call_user_func_array(array($this, 'parent::__construct'), func_get_args());
    }

    /**
     * Filter files against the regex. Add to filtered array if not accepted.
     * @return string
     */
    public function accept()
    {
        $accepted = (! $this->isFile() || preg_match($this->regex, strtolower($this->getFilename())));

        if ($this->isFile()) {
            (! $accepted) && ($this->filtered[] = $this->getPathName());
        } else {
            (! in_array($this->getFilename(), ['.', '..'])) && ($this->folders[] = $this->getPathName());
        }

        return $accepted;
    }
}

/* End of file */
