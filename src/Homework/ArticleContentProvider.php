<?php

namespace App\Homework;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Twig\Node\TextNode;

class ArticleContentProvider implements ArticleContentProviderInterface
{
    /**
     * @var MarkdownParserInterface
     */
    private $markdownParser;

    /**
     * @var bool
     */
    private $viewBold;

    /**
     * @var array
     */
    private $paragraphs;

    /**
     * @param MarkdownParserInterface $markdownParser
     * @param $viewBold
     * @param $pathToDirWithParagraphs
     * @throws \Exception
     */
    public function __construct(MarkdownParserInterface $markdownParser, $viewBold, $pathToDirWithParagraphs)
    {
//        $this->markdownParser = $markdownParser;
//        $this->viewBold = $viewBold;
//        $this->paragraphs = $this->getParagraphs($pathToDirWithParagraphs);
    }

    /**
     * @param int $paragraphNumbers
     * @param string|null $word
     * @param int $wordsCount
     * @return string
     */
    public function get(int $paragraphNumbers, string $word = null, int $wordsCount = 0): string
    {
        $paragraphs = [];
        for ($i = 0; $i < $paragraphNumbers; $i++) {
            $paragraph = $this->pickRandomParagraph();
            $paragraphs[] = $this->convertMarkdownTextToHtml($paragraph);
        }

        if ($word) {
            $paragraphs = $this->pasteWordInRandomPlacesInParagraphs($paragraphs, $word, $wordsCount);
        }

        return implode($paragraphs);
    }

    /**
     * @param array $paragraphs
     * @param string $word
     * @param int $count
     *
     * @return array
     */
    protected function pasteWordInRandomPlacesInParagraphs($paragraphs, $word, $count)
    {
        $textNodes = [];

        foreach ($paragraphs as $index => $paragraph) {
            $dom = new \DOMDocument();
            $dom->loadHTML($paragraph, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $paragraphs[$index] = $dom;
            $textNodes = array_merge($textNodes, $this->findTextNodesInDom($dom->childNodes));
        }

        for ($i = 0; $i < $count; $i++) {
            $randomNodeIndex = rand(0, count($textNodes) - 1);
            $randomTextNode = $textNodes[$randomNodeIndex];
            $words = explode(' ', $randomTextNode->nodeValue);
            $randomPlaceInPhrase = rand(0, count($words) - 1);
            array_splice($words, $randomPlaceInPhrase, 0, $word);

            $parent = $randomTextNode->parentNode;
            $parent->removeChild($randomTextNode);
            array_splice($textNodes, $randomNodeIndex, 1);

            for ($index = 0; $index < count($words); $index++) {
                if ($index == $randomPlaceInPhrase) {
                    $parent->appendChild($this->getWordNode($words[$index] . " "));
                } else {
                    $newNode = new \DOMText($words[$index] . " ");
                    $parent->appendChild($newNode);
                    $textNodes[] = $newNode;
                }
            }
        }

        foreach ($paragraphs as $index => $paragraph) {
            $paragraphs[$index] = $paragraph->saveHTML();
        }

        return $paragraphs;
    }

    /**
     * @param \DOMNodeList $node
     * @return array
     */
    public function findTextNodesInDom($node)
    {
        $textNodes = [];
        foreach ($node as $item) {
            if ($item->hasChildNodes()) {
                $textNodes = array_merge($textNodes, $this->findTextNodesInDom($item->childNodes));
            } else {
                if ($item->nodeType == XML_TEXT_NODE && trim($item->nodeValue)) {
                    $textNodes[] = $item;
                }
            }
        }

        return $textNodes;
    }


    /**
     * @param string $text
     *
     * @return string
     */
    protected function convertMarkdownTextToHtml($text)
    {
        return $this->markdownParser->transformMarkdown($text);
    }

    /**
     * @return string
     */
    protected function pickRandomParagraph()
    {
        $randomIndex = rand(0, count($this->paragraphs) - 1);
        return $this->paragraphs[$randomIndex];
    }

    /**
     * @param string $word
     *
     * @return \DOMElement
     *
     * @throws \DOMException
     */
    protected function getWordNode($word)
    {
        if ($this->viewBold) {
            return new \DOMElement('strong', $word);
        } else {
            return new \DOMElement('em', $word);
        }
    }

    /**
     * @param string $pathToDirWithParagraphs
     * @return array
     * @throws \Exception
     */
    private function getParagraphs($pathToDirWithParagraphs)
    {
        $paragraphs = [];
        if (!file_exists($pathToDirWithParagraphs) || !is_dir($pathToDirWithParagraphs)) {
            throw new \Exception('Путь до предъопределенных параграфов некорректен!');
        }
        $files = scandir($pathToDirWithParagraphs);

        foreach (array_diff($files, ['.', '..']) as $fileName) {
            $paragraphs[] = file_get_contents($pathToDirWithParagraphs . DIRECTORY_SEPARATOR . $fileName);
        }
        if (!count($paragraphs)) {
            throw new \Exception('Не найдены предъеопределенные параграфы');
        }
        return $paragraphs;
    }
}