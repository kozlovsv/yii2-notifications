<?php

namespace kozlovsv\notifications;

use yii\web\AssetBundle;

/**
 * Class NotificationsAsset
 *
 * @package kozlovsv\notifications
 */
class NotificationsAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__.'/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/notifications.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/notifications.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];

}
