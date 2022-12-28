<?php
/*
 * Yii2 IDE Autocomplete Helper
 *
 * @author  Vitaliy IIIFX Khomenko (c) 2021
 * @license MIT
 *
 * @link    https://github.com/iiifx-production/yii2-autocomplete-helper
 */

class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication
     */
    public static $app;
}

/**
 * @property yii\caching\FileCache $cache
 * @property yii\db\Connection $db
 * @property yii\symfonymailer\Mailer $mailer
 * @property iiifx\Yii2\Autocomplete\Component $autocomplete
 * @property yii\web\UrlManager $urlManager
 * @property yii\redis\Connection $redis
 */
abstract class BaseApplication extends \yii\base\Application {}

/**
 * @property yii\caching\FileCache $cache
 * @property yii\db\Connection $db
 * @property yii\symfonymailer\Mailer $mailer
 * @property iiifx\Yii2\Autocomplete\Component $autocomplete
 * @property yii\web\UrlManager $urlManager
 * @property yii\redis\Connection $redis
 */
class WebApplication extends \yii\web\Application {}

/**
 * @property yii\caching\FileCache $cache
 * @property yii\db\Connection $db
 * @property yii\symfonymailer\Mailer $mailer
 * @property iiifx\Yii2\Autocomplete\Component $autocomplete
 * @property yii\web\UrlManager $urlManager
 * @property yii\redis\Connection $redis
 */
class ConsoleApplication extends \yii\console\Application {}
