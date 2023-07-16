<?php

namespace App\Twig\Runtime;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    /**
     * @var MarkdownParserInterface
     */
    public function __construct(MarkdownParserInterface $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parser($content)
    {
        return $this->markdownParser->transformMarkdown($content);
    }
}
