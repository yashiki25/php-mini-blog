<?php

/**
 * 投稿にまつわるコントローラー
 */
class StatusController extends Controller
{
    protected $authActions = ['index', 'post'];

    /**
     * 投稿画面を表示
     * @return false|string
     */
    public function indexAction()
    {
        $user = $this->session->get('user');
        $statuses = $this->dbManager->get('Status')
            ->fetchAllPersonalArchivesByUserId($user['id']);

        return $this->render([
            'statuses' => $statuses,
            'body'     => '',
            '_token'   => $this->generateCsrfToken('status/post'),
        ]);
    }

    /**
     * 投稿を保存
     * @return false|string|void
     * @throws HttpNotFoundException
     */
    public function postAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('status/post', $token)) {
            return $this->redirect('/');
        }

        $body = $this->request->getPost('body');

        // バリデーション
        $errors = [];
        if (!mb_strlen($body)) {
            $errors[] = 'ひとことを入力してください';
        } else if (mb_strlen($body) > 200) {
            $errors[] = 'ひとことは200文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $user = $this->session->get('user');
            $this->dbManager->get('Status')->insert($user['id'], $body);

            return $this->redirect('/');
        }

        $user = $this->session->get('user');
        $statuses = $this->dbManager->get('Status')
            ->fetchAllPersonalArchivesByUserId($user['id']);

        return $this->render([
            'errors'   => $errors,
            'body'     => $body,
            'statuses' => $statuses,
            '_token'   => $this->generateCsrfToken('status/post'),
        ], 'index');
    }

    /**
     * ユーザー情報を取得
     * @param $params
     * @return false|string
     * @throws HttpNotFoundException
     */
    public function userAction($params)
    {
        $user = $this->dbManager->get('User')
            ->fetchByUserName($params['user_name']);

        if (!$user) {
            $this->forward404();
        }

        $statuses = $this->dbManager->get('Status')
            ->fetchAllByUserId($user['id']);

        $following = null;
        if ($this->session->isAuthenticated()) {
            $my = $this->session->get('user');
            if ($my['id'] !== $user['id']) {
                $following = $this->dbManager->get('Following')
                    ->isFollowing($my['id'], $user['id']);
            }
        }

        return $this->render([
            'user'      => $user,
            'statuses'  => $statuses,
            'following' => $following,
            '_token'    => $this->generateCsrfToken('account/follow')
        ]);
    }

    /**
     * 投稿詳細を表示
     * @param $params
     * @return false|string
     * @throws HttpNotFoundException
     */
    public function showAction($params)
    {
        $status = $this->dbManager->get('Status')
            ->fetchByIdAndUserName($params['id'], $params['user_name']);

        if (!$status) {
            $this->forward404();
        }

        return $this->render(['status' => $status]);
    }
}