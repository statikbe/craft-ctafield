<?php
/**
 * CTA plugin for Craft CMS 3.x
 *
 * Call to action & link fields made easy
 *
 * @link      https://www.statik.be
 * @copyright Copyright (c) 2018 Statik
 */

namespace statikbe\cta\models;

use statikbe\cta\CTA;

use Craft;
use craft\base\Model;

/**
 * @author    Statik
 * @package   CTA
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    public $classes = [];

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
