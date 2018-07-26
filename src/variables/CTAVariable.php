<?php
/**
 * CTA plugin for Craft CMS 3.x
 *
 * Call to action & link fields made easy
 *
 * @link      https://www.statik.be
 * @copyright Copyright (c) 2018 Statik
 */

namespace statikbe\cta\variables;

use statikbe\cta\CTA;

use Craft;

/**
 * @author    Statik
 * @package   CTA
 * @since     1.0.0
 */
class CTAVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }
}
