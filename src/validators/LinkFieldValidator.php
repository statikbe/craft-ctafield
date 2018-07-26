<?php

namespace statikbe\cta\validators;

use statikbe\cta\fields\LinkField;
use statikbe\cta\models\Link;
use yii\validators\Validator;

/**
 * Class LinkFieldValidator
 * @package ctafield
 */
class LinkFieldValidator extends Validator
{
  /**
   * @var LinkField
   */
  public $field;

  /**
   * @param mixed $value
   * @return array|null
   */
  protected function validateValue($value) {
    if ($value instanceof Link) {
      $linkType = $value->getLinkType();

      if (!is_null($linkType)) {
        return $linkType->validateValue($this->field, $value);
      }
    }

    return null;
  }
}
