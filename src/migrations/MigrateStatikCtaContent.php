<?php

namespace statikbe\cta\migrations;

use Craft;
use craft\db\Query;
use craft\fieldlayoutelements\CustomField;
use craft\helpers\Db;
use craft\helpers\ElementHelper;
use craft\helpers\Json;
use statikbe\cta\fields\CTAField;
use verbb\hyper\base\ElementLink;
use verbb\hyper\fields\HyperField;
use verbb\hyper\links as linkTypes;

use craft\helpers\Console;
use craft\helpers\StringHelper;


class MigrateStatikCtaContent extends PluginContentMigration
{
    // Properties
    // =========================================================================

    public array $typeMap = [
        'asset' => linkTypes\Asset::class,
        'category' => linkTypes\Category::class,
        'email' => linkTypes\Email::class,
        'entry' => linkTypes\Entry::class,
        'tel' => linkTypes\Phone::class,
        'product' => linkTypes\Product::class,
        'url' => linkTypes\Url::class,
        'custom' => linkTypes\Url::class,
    ];

    public string $oldFieldTypeClass = CTAField::class;
    public bool $resaveFields = false;

    // Public Methods
    // =========================================================================

    public function convertModel(HyperField $field, array $oldSettings): bool|array|null
    {
        $oldType = $oldSettings['type'] ?? null;
        $hyperType = $oldSettings[0]['type'] ?? null;

        if (str_contains($hyperType, 'verbb\\hyper')) {
            $this->stdout('    > Content already migrated to Hyper content.', Console::FG_GREEN);

            return null;
        }

        // Return `null` for an empty field, or already migrated to Hyper.
        // `false` for when unable to find matching new type.
        if (!$oldType) {
            return null;
        }
        $linkTypeClass = $this->getLinkType($oldType);

        if (!$linkTypeClass) {
            $this->stdout("    > Unable to migrate “{$oldType}” class.", Console::FG_RED);
            return false;
        }

        /** @var linkTypes\Url $link */
        $link = new $linkTypeClass();
        $link->handle = 'default-' . StringHelper::toKebabCase($linkTypeClass);
        $link->linkValue = $oldSettings['value'] ?? null;
        $link->linkText = $oldSettings['customText'] ?? null;
        $link->newWindow = $oldSettings['target'] ?? false;
        $link->fields = [
            'linkClasses' =>$oldSettings['class'] ?? null
        ];
        return [$link->getSerializedValues()];
    }
}