<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 14.08.18
 * Time: 17:51
 */

namespace Fw\LastBundle\Router;

use Symfony\Component\Routing\Generator\UrlGenerator;

class FileSuffixUrlGenerator extends UrlGenerator
{
    const DEFAULT_SUFFIX = 'html';

    /**
     * Returns path with default suffix if no suffix is included.
     *
     * @param $path
     * @return string
     */
    static function appendSuffix(string $path) : string {

        if(empty($path) || strpos($path, '.') > -1) {
            return $path;
        }
        $path_parts = explode('?', $path);

        // If path ends with an "/", transform it to "/index".
        if(substr($path_parts[0], -1) === '/') {
            $path_parts[0] .= 'index';
        }

        $path_parts[0].= '.'.static::DEFAULT_SUFFIX;
        return join('?', $path_parts);
    }

    /**
     * {@inheritdoc}
     */
    public function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, array $requiredSchemes = []) {
        return static::appendSuffix(parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes));
    }
}