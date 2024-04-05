<?php
/**
 * CTA plugin for Craft CMS 3.x
 *
 * Call to action & link fields made easy
 *
 * @link      https://www.statik.be
 * @copyright Copyright (c) 2018 Statik
 */

namespace statikbe\cta;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use statikbe\cta\fields\CTAField as CTAFieldField;
use statikbe\cta\models\ElementLinkType;
use statikbe\cta\models\InputLinkType;
use statikbe\cta\models\LinkTypeInterface;
use statikbe\cta\models\Settings;
use yii\base\Event;
use yii\console\Application as ConsoleApplication;


/**
 * Class CTA
 *
 * @author    Statik
 * @package   CTA
 * @since     1.0.0
 *
 */
class CTA extends Plugin
{
    public const ENTRY = 'entry';
    public const EMAIL = 'email';
    public const TEL = 'tel';
    public const URL = 'url';
    public const CATEGORY = 'category';
    public const ASSET= 'asset';


    // Static Properties
    // =========================================================================

    /**
     * @var LinkTypeInterface[]
     */
    private $linkTypes;
    /**
     * @var CTA
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'statikbe\cta\console\controllers';
        }

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = CTAFieldField::class;
            }
        );

    }

    /**
     * @param string $name
     * @param LinkTypeInterface $type
     */
    public function addLinkType(string $name, LinkTypeInterface $type) {
        \Craft::$app->getDeprecator()->log(
            'statikbe\cta\\Plugin::addLinkType()',
            'statikbe\cta\\Plugin::addLinkType() is deprecated and will be removed. Use the event Plugin::EVENT_REGISTER_LINK_TYPES to add new link types.'
        );

        $this->getLinkTypes();
        $this->linkTypes[$name] = $type;
    }

    /**
     * @return LinkTypeInterface[]
     */
    public function getLinkTypes() {
        if (!isset($this->linkTypes)) {
            $this->linkTypes = $this->createDefaultLinkTypes();
        }
        return $this->linkTypes;
    }

    /**
     * @return LinkTypeInterface[]
     */
    private function createDefaultLinkTypes() {
        $result = [
            self::URL => new InputLinkType([
                'displayName'  => 'Url',
                'displayGroup' => 'Input fields',
                'inputType'    => 'url'
            ]),
            self::EMAIL => new InputLinkType([
                'displayName'  => 'Mail',
                'displayGroup' => 'Input fields',
                'inputType'    => 'email'
            ]),
            self::TEL => new InputLinkType([
                'displayName'  => 'Telephone',
                'displayGroup' => 'Input fields',
                'inputType'    => 'tel'
            ]),
            self::ENTRY => new ElementLinkType([
                'displayGroup' => 'Craft CMS',
                'elementType'  => \craft\elements\Entry::class
            ]),
            self::ASSET => new ElementLinkType([
                'displayGroup' => 'Craft CMS',
                'elementType'  => \craft\elements\Asset::class,
            ]),
            self::CATEGORY => new ElementLinkType([
                'displayGroup' => 'Craft CMS',
                'elementType'  => \craft\elements\Category::class
            ]),
        ];

        // Add craft commerce elements
        if (class_exists('craft\commerce\elements\Product')) {
            $result['craftCommerce-product'] = new ElementLinkType([
                'displayGroup' => 'Craft commerce',
                'elementType'  => 'craft\commerce\elements\Product'
            ]);
        }

        // Add solspace calendar elements
        if (class_exists('Solspace\Calendar\Elements\Event')) {
            $result['solspaceCalendar-event'] = new ElementLinkType([
                'displayGroup' => 'Solspace calendar',
                'elementType'  => 'Solspace\Calendar\Elements\Event'
            ]);
        }

        return $result;
    }

    /**
     * @param RegisterComponentTypesEvent $event
     */
    public function onRegisterFieldTypes(RegisterComponentTypesEvent $event) {
        $event->types[] = LinkField::class;
    }

    protected function createSettingsModel(): Model
    {
        return new Settings();
    }


}
