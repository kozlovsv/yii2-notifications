<?php

namespace kozlovsv\notifications\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use yii\data\Pagination;
use yii\helpers\Url;
use kozlovsv\notifications\helpers\TimeElapsed;
use kozlovsv\notifications\widgets\Notifications;

class DefaultController extends Controller
{
    /**
     * Displays index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->getUser()->getId();
        $query = (new Query())
            ->from('{{%notifications}}')
            ->andWhere(['user_id' => $userId]);

        $pagination = new Pagination([
            'pageSize' => 20,
            'totalCount' => $query->count(),
        ]);

        $list = $query
            ->orderBy(['id' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $notifs = $this->prepareNotifications($list);

        return $this->render('index', [
            'notifications' => $notifs,
            'pagination' => $pagination,
        ]);
    }

    public function actionList()
    {
        $userId = Yii::$app->getUser()->getId();
        $list = (new Query())
            ->from('{{%notifications}}')
            ->andWhere(['user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->limit(7)
            ->all();
        $notifs = $this->prepareNotifications($list);
        $this->ajaxResponse(['list' => $notifs]);
    }

    public function actionCount()
    {
        $count = Notifications::getCountUnseen();
        $this->ajaxResponse(['count' => $count]);
    }

    public function actionRead($id)
    {
        Yii::$app->getDb()->createCommand()->update('{{%notifications}}', ['read' => true, 'seen' => true], ['id' => $id, 'user_id' => Yii::$app->getUser()->getId()])->execute();

        if(Yii::$app->getRequest()->getIsAjax()){
            return $this->ajaxResponse(1);
        }

        return $this->redirect('index');
    }

    public function actionReadAll()
    {
        Yii::$app
            ->getDb()
            ->createCommand()
            ->update('{{%notifications}}', ['read' => true, 'seen' => true], ['user_id' => Yii::$app->getUser()->getId()])
            ->execute();
        if(Yii::$app->getRequest()->getIsAjax()){
            return $this->ajaxResponse(1);
        }

        Yii::$app->getSession()->setFlash('success', Yii::t('modules/notifications', 'All notifications have been marked as read.'));

        return $this->redirect('index');
    }

    public function actionDeleteAll()
    {
        Yii::$app->getDb()
            ->createCommand()
            ->delete('{{%notifications}}', ['user_id' => Yii::$app->getUser()->getId()])
            ->execute();

        if(Yii::$app->getRequest()->getIsAjax()){
            return $this->ajaxResponse(1);
        }

        Yii::$app->getSession()->setFlash('success', Yii::t('modules/notifications', 'All notifications have been deleted.'));

        return $this->redirect('index');
    }

    private function prepareNotifications($list){
        $notifs = [];
        $seen = [];
        foreach($list as $notif){
            if(!$notif['seen']){
                $seen[] = $notif['id'];
            }
            $route = @unserialize($notif['route']);
            $notif['url'] = !empty($route) ? Url::to($route) : '';
            $notif['timeago'] = TimeElapsed::timeElapsed($notif['created_at']);
            $notifs[] = $notif;
        }

        if(!empty($seen)){
            Yii::$app->getDb()->createCommand()->update('{{%notifications}}', ['seen' => true], ['id' => $seen])->execute();
        }

        return $notifs;
    }

    public function ajaxResponse($data = [])
    {
        if(is_string($data)){
            $data = ['html' => $data];
        }

        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes(true);
        foreach ($flashes as $type => $message) {
            $data['notifications'][] = [
                'type' => $type,
                'message' => $message,
            ];
        }
        return $this->asJson($data);
    }
}