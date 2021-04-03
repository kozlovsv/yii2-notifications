<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = Yii::t('modules/notifications', 'Notifications');
$js = "var readUrl = '" . Url::to(['/notifications/default/read']) . "';\r\n";
$js .= <<<JS
    $('li.notification-item').on('click', function(){
        var item = $(this);
        $.ajax({
            url: readUrl,
            type: "GET",
            data: {id: item.data('id')},
            dataType: "json",
            success: function (data) {
                item.addClass('read');
                item.children(".mark-read").remove();
            }
        });
    });
JS;

$this->registerJs($js, $this::POS_END);

?>
<div class="notification-index">
    <div class="page-header">
        <div class="buttons">
            <a class="btn btn-danger" href="<?= Url::toRoute(['/notifications/default/delete-all']) ?>"
               data-confirm="<?= Yii::t('modules/notifications', 'Are you sure you want to delete all notification?'); ?>"><?= Yii::t('modules/notifications', 'Delete all'); ?></a>
            <a class="btn btn-warning" href="<?= Url::toRoute(['/notifications/default/read-all']) ?>"
               data-confirm="<?= Yii::t('modules/notifications', 'Mark all messages as read?'); ?>"><?= Yii::t('modules/notifications', 'Mark all as read'); ?></a>
        </div>
        <h1>
            <span class="glyphicon glyphicon-bell"></span>
            <?= Yii::t('modules/notifications', 'Notifications') ?>
        </h1>
    </div>

    <div class="page-content">
        <ul id="notifications-items">
            <?php if ($notifications): ?>
                <?php foreach ($notifications as $notif): ?>
                    <li class="notification-item<?php if ($notif['read']): ?> read<?php endif; ?>"
                        data-id="<?= $notif['id']; ?>" data-key="<?= $notif['key']; ?>">
                        <a href="<?= $notif['url'] ?>" target="_blank">
                            <span class="icon"></span>
                            <span class="message"><?= Html::encode($notif['message']); ?></span>
                        </a>
                        <small class="timeago"><?= $notif['timeago']; ?></small>
                        <?php if (!$notif['read']): ?>
                        <a href="<?= Url::toRoute(['/notifications/default/read', 'id' => $notif['id']]) ?>"
                           class="mark-read"
                           title="<?= Yii::t('modules/notifications', 'Mark as read') ?>">
                        </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="empty-row"><?= Yii::t('modules/notifications', 'There are no notifications to show') ?></li>
            <?php endif; ?>
        </ul>

        <?= LinkPager::widget(['pagination' => $pagination]); ?>
    </div>
</div>
