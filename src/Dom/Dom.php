<?php

namespace Kynda\Dom;

use DOMDocument;
use DOMNodeList;
use DOMXPath;

class Dom
{
    protected DOMDocument $doc;

    public function __construct(string $html)
    {
        $this->doc = new DOMDocument();

        // use LibXML internal error handler to prevent errors from bubbling to PHP
        libxml_use_internal_errors(true);
        $this->doc->loadHTML('<?xml encoding="UTF-8">' . $html, \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD);
        libxml_clear_errors(); // clear all libxml errors
    }

    public function each(callable $callback): self
    {
        foreach ($this->xpath('//*') as $node) {
            $callback($node);
        }

        return $this;
    }

    public function xpath(string $expression): DOMNodeList
    {
        return (new DOMXPath($this->doc))->query($expression);
    }

    public function html(): string
    {
        // Note: 23 = strlen('<?xml encoding="UTF-8">')
        return trim(substr($this->doc->saveHTML(), 23));
    }

    public function __call($name, $arguments)
    {
        return $this->doc->{$name}(...$arguments);
    }

    public function __get($name)
    {
        return $this->doc->{$name};
    }
}
