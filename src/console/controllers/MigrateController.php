<?php

namespace statikbe\cta\console\controllers;

use Craft;
use craft\helpers\App;
use craft\helpers\Console;
use statikbe\configvaluesfield\fields\ConfigValuesFieldField;
use statikbe\cta\migrations\MigrateStatikCtaContent;
use statikbe\cta\migrations\MigrateStatikCtaField;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\console\ExitCode;

class MigrateController extends Controller
{
    public function actionStatikCtaField(): int
    {

        return $this->_migrate(MigrateStatikCtaField::class);
    }

    public function actionStatikCtaContent(): int
    {
        return $this->_migrate(MigrateStatikCtaContent::class);
    }


    private function _migrate(string $migrationClass): int
    {
        if (!Craft::$app->getPlugins()->isPluginInstalled('hyper') || !Craft::$app->getPlugins()->isPluginEnabled('hyper')) {
            $this->stderr("Hyper not installed, or not enabled in Craft. Please fix this before proceeding" . PHP_EOL, Console::FG_RED);
            return ExitCode::CANTCREAT;
        }

        if (!Craft::$app->getPlugins()->isPluginInstalled('config-values-field') || !Craft::$app->getPlugins()->isPluginEnabled('config-values-field')) {
            $this->stderr("Config Values Field not installed, or not enabled in Craft. Please fix this before proceeding" . PHP_EOL, Console::FG_RED);
            return ExitCode::CANTCREAT;
        }

        if (!Craft::$app->getFields()->getFieldByHandle('linkClasses')) {
            $classesField = new ConfigValuesFieldField();
            $classesField->handle = 'linkClasses';
            $classesField->name = "CTA Style";
            $classesField->dataSet = 'CTA styles';
            $classesField->groupId = 1;
            $classesField->validate();
            if (!$classesField->validate()) {
                throw new InvalidConfigException("Couldn't create field: {$classesField->getFirstError()}");
            }

            if (Craft::$app->getFields()->saveField($classesField)) {
                $this->stdout("linkClasses field created" . PHP_EOL, Console::FG_GREEN);
            }
        }

        App::maxPowerCaptain();
        $migration = new $migrationClass();
        $migration->setConsoleRequest($this);
        $migration->up();

        return ExitCode::OK;
    }
}
