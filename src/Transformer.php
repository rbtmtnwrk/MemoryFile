<?php
namespace MemoryFile;

class Transformer
{
    protected $exif;
    protected $splFileInfo;

    public function getDatePath($dateInt)
    {
        return strtoupper(date("Y/m", $dateInt) . '_' . date("M", $dateInt));
    }

    public function getDateFormat()
    {
        return "Y-m-d G:i:s";
    }

    public function setExif($exif)
    {
        $this->exif = $exif;

        return $this;
    }

    public function setSplFileInfo($splFileInfo)
    {
        $this->splFileInfo = $splFileInfo;

        return $this;
    }

    public function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public function isMac()
    {
        return PHP_OS == 'Darwin';
    }

    public function type($mimeType = null)
    {
        $mimeType = $mimeType ?: $this->mimeType();

        $mimePhoto = [
            'image/tiff' => 'tiff',
            'image/jpeg' =>  'jpg',
        ];

        $mimeMovie = [
            'video/quicktime' => 'mov',
            'video/avi'       => 'avi',
            'video/msvideo'   => 'avi',
            'video/x-msvideo' => 'avi',
        ];

        if (array_key_exists($mimeType, $mimePhoto)) {
            return 'photo';
        } else if (array_key_exists($mimeType, $mimeMovie)) {
            return 'movie';
        }

        return null;
    }

    public function hasIdf()
    {
        return $this->exif && isset($this->exif['IFD0']);
    }

    public function hasExif()
    {
        return $this->exif && isset($this->exif['EXIF']);
    }

    /**
     * Attempt to get the creation date of a file. If there is no
     * Exif date information we can get the create date for
     * Windows and Mac only. Linux we are SOL.
     * @return array
     */
    public function date()
    {
        $systemDate = $this->splFileInfo->getCTime();
        $systemDate = $this->isMac() ? $this->statDate() : $systemDate;
        $xifDate    = null;
        $nicePath   = null;

        $this->hasExif() && isset($this->exif['EXIF']['DateTimeOriginal']) && ($xifDate = strtotime($this->exif['EXIF']['DateTimeOriginal']));

        (! $xifDate) && $this->hasExif() && isset($this->exif['IDF0']['DateTime']) && ($xifDate = strtotime($this->exif['IDF0']['DateTime']));

        $xifDate && ($nicePath = $this->getDatePath($xifDate));

        (! $nicePath) && ($nicePath = $this->getDatePath($systemDate));

        $systemDate = date($this->getDateFormat(), $systemDate);
        $xifDate    = date($this->getDateFormat(), $xifDate);

        return compact('systemDate', 'xifDate', 'nicePath');
    }

    /**
     * Credits:
     * http://stackoverflow.com/questions/6176140/how-do-i-get-actual-creation-time-for-a-file-in-php-on-a-mac
     * http://stackoverflow.com/questions/29826010/how-to-use-the-stat-command-on-os-x-to-display-a-file-or-directories-creation-da
     * @param  string $path
     * @return string
     */
    public function statDate()
    {
        $intTime = null;

        if ($handle = popen('stat -f %B ' . escapeshellarg($this->splFileInfo->getPathName()), 'r')) {
            $intTime = trim(fread($handle, 100));
            pclose($handle);
        }

        return $intTime;
    }

    public function source()
    {
        if (! $this->hasIdf()) {
            return 'NO_SOURCE';
        }

        $source = [];
        array_key_exists('Model', $this->exif['IFD0']) && $source[] = $this->exif['IFD0']['Model'];
        (! $source) && array_key_exists('Make', $this->exif['IFD0']) && $source[] = $this->exif['IFD0']['Make'];
        $source = strtoupper(implode(' ', $source));
        $source = str_replace(' ', '_', $source);
        $source = str_replace('-', '_', $source);

        return $source;
    }

    public function mimeType()
    {
        if ($this->hasExif() && isset($this->exif['FILE']) && isset($this->exif['FILE']['MimeType'])) {
            return $this->exif['FILE']['MimeType'];
        }

        return mime_content_type($this->splFileInfo->getPathName());
    }

    public function transform()
    {
        $path   = $this->splFileInfo->getPathName();
        $source = $this->source();
        $mime   = $this->mimeType();
        $type   = $this->type($mime);
        $date   = $this->date();
        $suffix = $type == 'movie' ? 'MOVIES' : $source;
        $nice   = $date['nicePath'] . '/' . $suffix;

        return compact('path', 'source', 'mime', 'type', 'date', 'nice');
    }
}