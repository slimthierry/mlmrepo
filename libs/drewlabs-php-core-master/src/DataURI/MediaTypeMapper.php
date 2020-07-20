<?php

namespace Drewlabs\Core\DataURI;

class MediaTypeMapper
{
    /**
     * List of commonly use media types associated with their corresponding extension
     */
    const MIMES_MAP = array(
        array(
            'extension' => '.aac',
            'mime' => 'audio/aac',
        ),
        array(
            'extension' => '.abw',
            'mime' => 'application/x-abiword',
        ),
        array(
            'extension' => '.arc',
            'mime' => 'application/x-freearc',
        ),
        array(
            'extension' => '.avi',
            'mime' => 'video/x-msvideo',
        ),
        array(
            'extension' => '.azw',
            'mime' => 'application/vnd.amazon.ebook',
        ),
        array(
            'extension' => '.bin',
            'mime' => 'application/octet-stream',
        ),
        array(
            'extension' => '.bmp',
            'mime' => 'image/bmp',
        ),
        array(
            'extension' => '.bz',
            'mime' => 'application/x-bzip',
        ),
        array(
            'extension' => '.bz2',
            'mime' => 'application/x-bzip2',
        ),
        array(
            'extension' => '.csh',
            'mime' => 'application/x-csh',
        ),
        array(
            'extension' => '.css',
            'mime' => 'text/css',
        ),
        array(
            'extension' => '.csv',
            'mime' => 'text/csv',
        ),
        array(
            'extension' => '.doc',
            'mime' => 'application/msword',
        ),
        array(
            'extension' => '.docx',
            'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ),
        array(
            'extension' => '.docx',
            'mime' => 'application/vndopenxmlformats-officedocumentwordprocessingmldocument',
        ),
        array(
            'extension' => '.eot',
            'mime' => 'application/vnd.ms-fontobject',
        ),
        array(
            'extension' => '.epub',
            'mime' => 'application/epub+zip',
        ),
        array(
            'extension' => '.gz',
            'mime' => 'application/gzip',
        ),
        array(
            'extension' => '.gif',
            'mime' => 'image/gif',
        ),
        array(
            'extension' => '.html',
            'mime' => 'text/html',
        ),
        array(
            'extension' => '.htm',
            'mime' => 'text/html',
        ),
        array(
            'extension' => '.ico',
            'mime' => 'image/vnd.microsoft.icon',
        ),
        array(
            'extension' => '.ics',
            'mime' => 'text/calendar',
        ),
        array(
            'extension' => '.jar',
            'mime' => 'application/java-archive',
        ),
        array(
            'extension' => '.jpg',
            'mime' => 'image/jpg',
        ),
        array(
            'extension' => '.jpg',
            'mime' => 'image/jpeg',
        ),
        array(
            'extension' => '.js',
            'mime' => 'text/javascript',
        ),
        array(
            'extension' => '.json',
            'mime' => 'application/json',
        ),
        array(
            'extension' => '.jsonld',
            'mime' => 'application/ld+json',
        ),
        array(
            'extension' => 'midi',
            'mime' => 'audio/x-midi',
        ),
        array(
            'extension' => '.mid',
            'mime' => 'audio/midi',
        ),
        array(
            'extension' => '.mjs',
            'mime' => 'text/javascript',
        ),
        array(
            'extension' => '.mp3',
            'mime' => 'audio/mpeg',
        ),
        array(
            'extension' => '.mpeg',
            'mime' => 'video/mpeg',
        ),
        array(
            'extension' => '.mpkg',
            'mime' => 'application/vnd.apple.installer+xml',
        ),
        array(
            'extension' => '.odp',
            'mime' => 'application/vnd.oasis.opendocument.presentation',
        ),
        array(
            'extension' => '.ods',
            'mime' => 'application/vnd.oasis.opendocument.spreadsheet',
        ),
        array(
            'extension' => '.odt',
            'mime' => 'application/vnd.oasis.opendocument.text',
        ),
        array(
            'extension' => '.oga',
            'mime' => 'audio/ogg',
        ),
        array(
            'extension' => '.ogv',
            'mime' => 'video/ogg',
        ),
        array(
            'extension' => '.ogx',
            'mime' => 'application/ogg',
        ),
        array(
            'extension' => '.opus',
            'mime' => 'audio/opus',
        ),
        array(
            'extension' => '.otf',
            'mime' => 'font/otf',
        ),
        array(
            'extension' => '.png',
            'mime' => 'image/png',
        ),
        array(
            'extension' => '.pdf',
            'mime' => 'application/pdf',
        ),
        array(
            'extension' => '.php',
            'mime' => 'application/x-httpd-php',
        ),
        array(
            'extension' => '.ppt',
            'mime' => 'application/vnd.ms-powerpoint',
        ),
        array(
            'extension' => '.pptx',
            'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ),
        array(
            'extension' => '.pptx',
            'mime' => 'application/vndopenxmlformats-officedocumentpresentationmlpresentation'
        ),
        array(
            'extension' => '.rar',
            'mime' => 'application/vnd.rar',
        ),
        array(
            'extension' => '.rtf',
            'mime' => 'application/rtf',
        ),
        array(
            'extension' => '.sh',
            'mime' => 'application/x-sh',
        ),
        array(
            'extension' => '.svg',
            'mime' => 'image/svg+xml',
        ),
        array(
            'extension' => '.swf',
            'mime' => 'application/x-shockwave-flash',
        ),
        array(
            'extension' => '.tar',
            'mime' => 'application/x-tar',
        ),
        array(
            'extension' => '.tiff',
            'mime' => 'image/tiff',
        ),
        array(
            'extension' => '.tif',
            'mime' => 'image/tiff',
        ),
        array(
            'extension' => '.ts',
            'mime' => 'video/mp2t',
        ),
        array(
            'extension' => '.ttf',
            'mime' => 'font/ttf',
        ),
        array(
            'extension' => '.txt',
            'mime' => 'text/plain',
        ),
        array(
            'extension' => '.vsd',
            'mime' => 'application/vnd.visio',
        ),
        array(
            'extension' => '.wav',
            'mime' => 'audio/wav',
        ),
        array(
            'extension' => '.weba',
            'mime' => 'audio/webm',
        ),
        array(
            'extension' => '.webm',
            'mime' => 'video/webm',
        ),
        array(
            'extension' => '.webp',
            'mime' => 'image/webp',
        ),
        array(
            'extension' => '.woff',
            'mime' => 'font/woff',
        ),
        array(
            'extension' => '.woff2',
            'mime' => 'font/woff2',
        ),
        array(
            'extension' => '.xhtml',
            'mime' => 'application/xhtml+xml',
        ),
        array(
            'extension' => '.xls',
            'mime' => 'application/vnd.ms-excel',
        ),
        array(
            'extension' => '.xlsx',
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ),
        array(
            'extension' => '.xlsx',
            'mime' => 'application/vndopenxmlformats-officedocumentspreadsheetmlsheet'
        ),
        array(
            'extension' => '.xml',
            'mime' => 'application/xml',
        ),
        array(
            'extension' => '.xml',
            'mime' => 'text/xml',
        ),
        array(
            'extension' => '.xul',
            'mime' => 'application/vnd.mozilla.xul+xml',
        ),
        array(
            'extension' => '.zip',
            'mime' => 'application/zip',
        ),
        array(
            'extension' => '.3gp',
            'mime' => 'video/3gpp',
        ),
        array(
            'extension' => '.3g2',
            'mime' => 'video/3gpp2',
        ),
        array(
            'extension' => '.7z',
            'mime' => 'application/x-7z-compressed',
        ),
    );

    /**
     * Get a file extension from the provide mime type parameter
     *
     * @param string $mime
     * @return void
     */
    public function mapMimeToExtension($mime)
    {
        $columns = array_column(static::MIMES_MAP, 'mime');
        $extenstion = array_search($mime, $columns);
        $extenstion = static::search($columns, function ($value) use ($mime) {
            return $mime !== '' && mb_strpos($value, $mime) !== false;
        });
        if ($extenstion) {
            return ltrim(static::MIMES_MAP[$extenstion]['extension'], '.');
        }
        return null;
    }

    /**
     * Get a file mime type from the provided extension parameter
     *
     * @param string $mime
     * @return void
     */
    public function mapExtensionToMime($extenstion)
    {
        $columns = array_column(static::MIMES_MAP, 'extension');
        $extenstion = substr($extenstion, 0, strlen('.')) === '.' ? ($extenstion) : ('.' . $extenstion);
        $mime = array_search($extenstion, $columns);
        if ($mime) {
            return trim(static::MIMES_MAP[$mime]['mime']);
        }
        return null;
    }

    private static function search(array $arr, callable $callack)
    {
        foreach ($arr as $key => $v)
            if ($callack($v))
                return $key;
        return false;
    }
}
