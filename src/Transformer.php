<?php
namespace MemoryFile;

class Transformer
{
    protected $exif;
    protected $splFileInfo;
    protected $dateInfo;

    public function getDateInfo()
    {
        return $this->dateInfo;
    }

    public function formatDatePath($dateInt)
    {
        return strtoupper(date("Y/m", $dateInt) . '_' . date("M", $dateInt));
    }

    public function getDateFormat()
    {
        return "Y-m-d G:i:s";
    }

    public function getMimePhoto()
    {
        return [
            'image/tiff' => 'tiff',
            'image/gif'  =>  'gif',
            'image/jpeg' =>  'jpg|jpeg',
            'image/x-canon-crw' =>  'crw',
        ];
    }

    public function getMimeMovie()
    {
        return [
            'video/quicktime' => 'mov',
            'video/avi'       => 'avi',
            'video/msvideo'   => 'avi',
            'video/x-msvideo' => 'avi',
            'video/x-ms-wmv'  => 'wmv',
            'video/3gpp'      => 'mp4',
            'video/mp4'       => 'mp4',
        ];
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

        if (array_key_exists($mimeType, $this->getMimePhoto())) {
            return 'photo';
        } else if (array_key_exists($mimeType, $this->getMimeMovie())) {
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
    public function parseFileDate()
    {
        $xifDate    = null;
        $systemDate = $this->splFileInfo->getCTime();
        $systemDate = $this->isMac() ? $this->statDate() : $systemDate;

        if ($this->hasExif()) {
            isset($this->exif['EXIF']['DateTimeOriginal']) && ($xifDate = strtotime($this->exif['EXIF']['DateTimeOriginal']));

            (! $xifDate) && isset($this->exif['IDF0']['DateTime']) && ($xifDate = strtotime($this->exif['IDF0']['DateTime']));
        }

        $this->dateInfo = [
            'xifDate'    => date($this->getDateFormat(), $xifDate),
            'systemDate' => date($this->getDateFormat(), $systemDate),
        ];

        if ($xifDate) {
            return $xifDate;
        }

        return $systemDate;
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
        $nosource = 'NO_SOURCE';
        if (! $this->hasIdf()) {
            return $nosource;
        }

        $source = [];
        array_key_exists('Model', $this->exif['IFD0']) && ($source[] = $this->exif['IFD0']['Model']);
        (! $source) && array_key_exists('Make', $this->exif['IFD0']) && ($source[] = $this->exif['IFD0']['Make']);

        if ($source) {
            $source = strtoupper(implode(' ', $source));
            $source = str_replace(' ', '_', $source);
            $source = str_replace('-', '_', $source);
        } else {
            $source = $nosource;
        }

        return $source;
    }

    public function mimeType()
    {
        if ($this->hasExif() && isset($this->exif['FILE']) && isset($this->exif['FILE']['MimeType'])) {
            return $this->exif['FILE']['MimeType'];
        }

        return mime_content_type($this->splFileInfo->getPathName());
    }

    public function formatBaseName($fileDate)
    {
        $datePrefix = date('dHis_', $fileDate);
        return $datePrefix . $this->splFileInfo->getBaseName();
    }

    public function transform()
    {
        $fileDate  = $this->parseFileDate();
        $mime      = $this->mimeType();
        $type      = $this->type($mime);
        $source    = $this->source();
        $subFolder = $type == 'movie' ? 'MOVIES' : $source;

        return [
            'path'        => $this->splFileInfo->getPathName(),
            'name'        => $this->formatBaseName($fileDate),
            'source'      => $source,
            'mime'        => $mime,
            'type'        => $type,
            'fileDates'   => $this->getDateInfo(),
            'subFolder'   => $subFolder,
            'folder'      => $this->formatDatePath($fileDate),
            'exif'        => $this->exif,
            'splFileInfo' => $this->splFileInfo,
        ];
    }
};