<?php

namespace statikbe\cta\models;

use Craft;
use craft\base\ElementInterface;
use craft\elements\Entry;
use craft\elements\conditions\ElementCondition;
use craft\helpers\Cp;
use craft\helpers\Html;
use statikbe\cta\fields\CTAField;
use yii\base\Model;

/**
 * Class ElementLinkType
 * @package cta\models
 */
class ElementLinkType extends Model implements LinkTypeInterface
{
    /**
     * @var ElementInterface
     */
    public $elementType;

    /**
     * @var string
     */
    public $displayGroup = 'Common';


    /**
     * ElementLinkType constructor.
     * @param string|array $elementType
     * @param array $options
     */
    public function __construct($elementType, array $options = [])
    {
        if (is_array($elementType)) {
            $options = $elementType;
        } else {
            $options['elementType'] = $elementType;
        }

        parent::__construct($options);
    }

    /**
     * @return array
     */
    public function getDefaultSettings(): array
    {
        return [
            'sources' => '*',
        ];
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        $elementType = $this->elementType;
        return $elementType::displayName();
    }

    /**
     * @return string
     */
    public function getDisplayGroup(): string
    {
        return \Craft::t('cta', $this->displayGroup);
    }

    public function getElement(CTA $link)
    {

        if ($this->isEmpty($link)) {
            return null;
        }

        $pluginSettings = \statikbe\cta\CTA::getInstance()->getSettings();
        $query = [
            'id' => $link->value,
            'site' => $pluginSettings->crossSiteLinking ? '*' : $link->getOwnerSite(),
        ];

        if (\Craft::$app->request->getIsCpRequest()) {
            $query += [
                'status' => null,
            ];
        }

        $elementType = $this->elementType;
        return $elementType::findOne($query);
    }

    public function getInputHtml(string $linkTypeName, CTAField $field, CTA $value, ElementInterface $element): string
    {
        $settings   = $field->getLinkTypeSettings($linkTypeName, $this);

        $selectionCondition = $field->getSelectionCondition();
        if ($selectionCondition instanceof ElementCondition) {
            $selectionCondition->referenceElement = $element;
        }

        $sources    = $settings['sources'];
        $isSelected = $value->type === $linkTypeName;
        $elements   = $isSelected ? array_filter([$this->getElement($value)]) : null;

        $criteria = [
            'status' => null,
        ];

        try {
            if(isset($settings['siteId']) && $settings['siteId'] != '*') {
                $criteria['siteId'] = $settings['siteId'] ;
            } else {
                $criteria['siteId'] = $this->getTargetSiteId($element);
            }
        } catch (\Exception $e) {
        }

        $selectFieldOptions = [
            'criteria'        => $criteria,
            'showSiteMenu'    => true,
            'elementType'     => $this->elementType,
            'condition'       => $selectionCondition,
            'elements'        => $elements,
            'id'              => $field->handle . '-' . $linkTypeName,
            'limit'           => 1,
            'name'            => $field->handle . '[' . $linkTypeName . ']',
            'storageKey'      => 'field.' . $field->handle,
            'sources'         => $sources === '*' ? null : $sources,
            'sourceElementId' => !empty($element->id) ? $element->id : null,
        ];

        try {
            return \Craft::$app->view->renderTemplate('cta/_input-element', [
                'isSelected'         => $isSelected,
                'linkTypeName'       => $linkTypeName,
                'selectFieldOptions' => $selectFieldOptions,
            ]);
        } catch (\Throwable $exception) {

            return Html::tag('p', \Craft::t(
                'cta',
                'Error: Could not render the template for the field `{name}`.',
                [ 'name' => $this->getDisplayName() ]
            ));
        }
    }

    /**
     * @param ElementInterface|null $element
     * @return int
     * @throws \craft\errors\SiteNotFoundException
     */
    protected function getTargetSiteId(ElementInterface $element = null): int
    {
        if (\Craft::$app->getIsMultiSite()) {
            if ($element !== null) {
                return $element->siteId;
            }
        }

        return \Craft::$app->getSites()->getCurrentSite()->id;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function getLinkValue($value)
    {
        return is_array($value) ? $value[0] : null;
    }

    public function getSettingsHtml(string $linkTypeName, CTAField $field): string
    {
        try {

            if ($linkTypeName === \statikbe\cta\CTA::ENTRY) {
                $selectionCondition = $field->getSelectionCondition() ?? $field->createSelectionCondition();
                if ($selectionCondition) {
                    $selectionCondition->mainTag = 'div';
                    $selectionCondition->id = 'selection-condition';
                    $selectionCondition->name = 'selectionCondition';
                    $selectionCondition->forProjectConfig = true;
                    $selectionCondition->queryParams[] = 'site';
                    $selectionCondition->queryParams[] = 'status';

                    $selectionConditionHtml = Cp::fieldHtml($selectionCondition->getBuilderHtml(), [
                        'label' => Craft::t('app', 'Selectable {type} Condition', [
                            'type' => Entry::pluralDisplayName(),
                        ]),
                        'instructions' => Craft::t('app', 'Only allow {type} to be selected if they match the following rules:', [
                            'type' => Entry::pluralLowerDisplayName(),
                        ]),
                    ]);
                }
            }


            return \Craft::$app->view->renderTemplate('cta/_settings-element', [
                'settings'     => $field->getLinkTypeSettings($linkTypeName, $this),
                'selectionCondition' => $selectionConditionHtml ?? null,
                'elementName'  => $this->getDisplayName(),
                'linkTypeName' => $linkTypeName,
                'sources'      => $this->getSources(),
            ]);
        } catch (\Throwable $exception) {
            dd($exception->getMessage());
            return Html::tag('p', \Craft::t(
                'cta',
                'Error: Could not render the template for the field `{name}`.',
                [ 'name' => $this->getDisplayName() ]
            ));
        }
    }

    /**
     * @return array
     */
    protected function getSources()
    {
        $elementType = $this->elementType;
        $options = array();
        foreach ($elementType::sources('settings') as $source) {
            if (array_key_exists('key', $source) && $source['key'] !== '*') {
                $options[$source['key']] = $source['label'];
            }
        }

        return $options;
    }

    /**
     * @param Link $link
     * @return null|string
     */
    public function getText(CTA $link)
    {
        $element = $this->getElement($link);
        if (is_null($element)) {
            return null;
        }

        return (string)$element;
    }

    /**
     * @param Link $link
     * @return null|string
     */
    public function getUrl(CTA $link)
    {
        $element = $this->getElement($link);
        if (is_null($element)) {
            return null;
        }

        return $element->getUrl();
    }

    /**
     * @param Link $link
     * @return bool
     */
    public function hasElement(CTA $link): bool
    {
        $element = $this->getElement($link);
        return !is_null($element);
    }

    /**
     * @param Link $link
     * @return bool
     */
    public function isEmpty(CTA $link): bool
    {
        if (is_numeric($link->value)) {
            return $link->value <= 0;
        }

        return true;
    }

    /**
     * @param LinkField $field
     * @param Link $link
     * @return array|null
     */
    public function validateValue(CTAField $field, CTA $link)
    {
        return null;
    }
}
