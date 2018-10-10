<?php

namespace statikbe\cta\models;

use craft\base\ElementInterface;
use statikbe\cta\fields\CTAField;

/**
 * Interface LinkTypeInterface
 * @package statikbe\cta\models
 */
interface LinkTypeInterface
{
    /**
     * @return array
     */
    public function getDefaultSettings(): array;

    /**
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * @return string
     */
    public function getDisplayGroup(): string;

    /**
     * @param Link $link
     * @return null|ElementInterface
     */
    public function getElement(CTA $link);

    /**
     * @param string $linkTypeName
     * @param CTAField $field
     * @param Link $value
     * @param ElementInterface $element
     * @return string
     */
    public function getInputHtml(string $linkTypeName, CTAField $field, CTA $value, ElementInterface $element): string;

    /**
     * @param mixed $value
     * @return mixed
     */
    public function getLinkValue($value);

    /**
     * @param string $linkTypeName
     * @param CTAField $field
     * @return string
     */
    public function getSettingsHtml(string $linkTypeName, CTAField $field): string;

    /**
     * @param Link $link
     * @return null|string
     */
    public function getText(CTA $link);

    /**
     * @param Link $link
     * @return null|string
     */
    public function getUrl(CTA $link);

    /**
     * @param Link $link
     * @return bool
     */
    public function hasElement(CTA $link): bool;

    /**
     * @param Link $link
     * @return bool
     */
    public function isEmpty(CTA $link): bool;

    /**
     * @param CTAField $field
     * @param Link $link
     * @return array|null
     */
    public function validateValue(CTAField $field, CTA $link);
}
