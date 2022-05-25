<?php

declare(strict_types=1);

namespace CaptchaLocalResolver\Actions;

use CaptchaLocalResolver\ActionInterface;
use CaptchaLocalResolver\Exceptions\ExecuteException;
use finfo;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;

class LocalResource implements ActionInterface
{
    private string $webroot;

    private string $path;

    public function __construct(string $webroot, string $path)
    {
        $this->webroot = $webroot;
        $this->path = $path;
    }

    public function execute(array $arguments): ResponseInterface
    {
        $absolutePath = $this->webroot . '/' . $this->satinizePath($this->path);

        if (file_exists($absolutePath) && is_file($absolutePath) && is_readable($absolutePath)) {
            $contentType = $this->readFileContentType($absolutePath); // RFC 2045
            $headers = array_filter(['Content-Type' => $contentType]); // if not known content type do not send any value
            return new Response(200, $headers, file_get_contents($absolutePath) ?: '');
        }

        throw ExecuteException::pathNotFound($this->path);
    }

    public static function satinizePath(string $path): string
    {
        // simplify the path
        $parts = static::simplifyPath($path);

        // remove any ".." from the beginning
        foreach ($parts as $i => $part) {
            if ('..' === $part) {
                unset($parts[$i]);
            } else {
                break;
            }
        }

        return implode('/', $parts);
    }

    /**
     * Simplify a path and return its parts as an array
     *
     * @param string $path
     * @return string[]
     */
    public static function simplifyPath(string $path): array
    {
        $parts = explode('/', parse_url($path, PHP_URL_PATH) ?: '');
        $parts = array_values(array_filter($parts, fn (string $name): bool => '.' !== $name && '' !== $name));

        // is ".." and previous is not "..", for paths like "../../some/path"
        $count = count($parts);
        for ($i = 1; $i < $count; $i = $i + 1) {
            if ('..' === $parts[$i] && '..' !== $parts[$i - 1]) {
                unset($parts[$i - 1]);
                unset($parts[$i]);
            }
        }
        return array_values($parts);
    }

    public static function readFileContentType(string $path): string
    {
        $mime = (new finfo())->file($path, FILEINFO_MIME) ?: '';
        if ('inode/x-empty' === substr($mime, 0, 13)) {
            $mime = '';
        }
        return str_replace('; charset=us-ascii', '; charset=utf-8', $mime);
    }
}
