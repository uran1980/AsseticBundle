<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Bundle\AsseticBundle\Twig;

use Assetic\Asset\AssetInterface;
use Assetic\Extension\Twig\AsseticTokenParser as BaseAsseticTokenParser;
use Symfony\Bundle\AsseticBundle\Exception\InvalidBundleException;
use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * Assetic token parser.
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
class AsseticTokenParser extends BaseAsseticTokenParser
{
    private $templateNameParser;
    private $enabledBundles;

    public function setTemplateNameParser(TemplateNameParserInterface $templateNameParser)
    {
        $this->templateNameParser = $templateNameParser;
    }

    public function setEnabledBundles(array $enabledBundles = null)
    {
        $this->enabledBundles = $enabledBundles;
    }

    public function parse(\Twig_Token $token)
    {
        if ($this->templateNameParser && is_array($this->enabledBundles)) {
            // check the bundle
            $templateRef = $this->templateNameParser->parse($this->parser->getStream()->getFilename());
            $bundle = $templateRef->get('bundle');
            if ($bundle && !in_array($bundle, $this->enabledBundles)) {
                throw new InvalidBundleException($bundle, "the {% {$this->getTag()} %} tag", $templateRef->getLogicalName(), $this->enabledBundles);
            }
        }

        return parent::parse($token);
    }

    protected function createNode(AssetInterface $asset, \Twig_NodeInterface $body, array $inputs, array $filters, $name, array $attributes = array(), $lineno = 0, $tag = null)
    {
        return new AsseticNode($asset, $body, $inputs, $filters, $name, $attributes, $lineno, $tag);
    }
}
