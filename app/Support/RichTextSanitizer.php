<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

class RichTextSanitizer
{
    private const ALLOWED_TAG_STRING = '<p><br><strong><b><em><i><u><s><h2><h3><h4><blockquote><ol><ul><li><a><hr><pre><code><div><span><img>';

    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'h2', 'h3', 'h4',
        'blockquote', 'ol', 'ul', 'li', 'a', 'hr', 'pre', 'code', 'div', 'span', 'img',
    ];

    private const ALIGNABLE_TAGS = ['p', 'h2', 'h3', 'h4', 'blockquote', 'li', 'div', 'pre'];

    private const MAX_IMAGE_DATA_URI_LENGTH = 2_900_000;

    public static function clean(string $content): string
    {
        $content = trim(strip_tags($content, self::ALLOWED_TAG_STRING));

        if ($content === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><div id="ubp-rich-text-root">'.$content.'</div></body></html>',
            LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = (new DOMXPath($document))->query('//*[@id="ubp-rich-text-root"]')->item(0);

        if (! $root instanceof DOMElement) {
            return '';
        }

        self::sanitizeChildren($root);

        $html = '';
        foreach ($root->childNodes as $child) {
            $html .= $document->saveHTML($child);
        }

        return trim($html);
    }

    private static function sanitizeChildren(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            self::sanitizeNode($child);
        }
    }

    private static function sanitizeNode(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            $tag = strtolower($node->tagName);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                self::unwrapNode($node);

                return;
            }

            if ($tag === 'img') {
                self::sanitizeImage($node);

                return;
            }

            self::sanitizeElementAttributes($node, $tag);
        }

        self::sanitizeChildren($node);
    }

    private static function sanitizeElementAttributes(DOMElement $node, string $tag): void
    {
        $href = $node->getAttribute('href');
        $style = $node->getAttribute('style');
        $align = $node->getAttribute('align');

        self::removeAttributes($node);

        if ($tag === 'a') {
            $href = self::cleanUrl($href);
            $node->setAttribute('href', $href ?: '#');

            if ($href && $href !== '#') {
                $node->setAttribute('target', '_blank');
                $node->setAttribute('rel', 'noreferrer');
            }
        }

        if (in_array($tag, self::ALIGNABLE_TAGS, true)) {
            $alignment = self::extractAlignment($style, $align);

            if ($alignment) {
                $node->setAttribute('style', 'text-align: '.$alignment.';');
            }
        }
    }

    private static function sanitizeImage(DOMElement $node): void
    {
        $src = self::cleanImageSource($node->getAttribute('src'));

        if (! $src) {
            $node->parentNode?->removeChild($node);

            return;
        }

        $alt = trim(strip_tags($node->getAttribute('alt')));

        self::removeAttributes($node);
        $node->setAttribute('src', $src);
        $node->setAttribute('alt', mb_substr($alt, 0, 120));
        $node->setAttribute('loading', 'lazy');
    }

    private static function removeAttributes(DOMElement $node): void
    {
        foreach (iterator_to_array($node->attributes) as $attribute) {
            $node->removeAttribute($attribute->name);
        }
    }

    private static function unwrapNode(DOMElement $node): void
    {
        $parent = $node->parentNode;

        if (! $parent) {
            return;
        }

        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }

    private static function extractAlignment(string $style, string $align): ?string
    {
        if (preg_match('/(?:^|;)\s*text-align\s*:\s*(left|right|center|justify)\s*(?:;|$)/i', $style, $match)) {
            return strtolower($match[1]);
        }

        if (preg_match('/^(left|right|center|justify)$/i', trim($align), $match)) {
            return strtolower($match[1]);
        }

        return null;
    }

    private static function cleanUrl(string $url): ?string
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($url === '') {
            return null;
        }

        if (preg_match('/^(https?:|mailto:|tel:)/i', $url) || str_starts_with($url, '#')) {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return null;
        }

        return preg_match('/^[a-z][a-z0-9+.-]*:/i', $url) ? null : $url;
    }

    private static function cleanImageSource(string $src): ?string
    {
        $src = trim(html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($src === '') {
            return null;
        }

        if (preg_match('/^data:image\/(?:png|jpe?g|gif|webp);base64,[a-z0-9+\/=\r\n]+$/i', $src)) {
            return strlen($src) <= self::MAX_IMAGE_DATA_URI_LENGTH ? $src : null;
        }

        if (preg_match('/^https?:\/\//i', $src) || str_starts_with($src, '/')) {
            return $src;
        }

        if (str_starts_with($src, '//')) {
            return null;
        }

        return preg_match('/^[a-z][a-z0-9+.-]*:/i', $src) ? null : $src;
    }
}
