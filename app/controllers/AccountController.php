<?php

/**
 * アカウント情報にまつわるコントローラー
 */
class AccountController extends Controller
{
    protected $authActions = ['index', 'signout', 'follow'];

    /**
     * サインアップ
     * @return false|string|void
     */
    public function signupAction()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        return $this->render([
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signup'),
        ]);
    }

    /**
     * サインイン
     * @return false|string|void
     */
    public function signinAction()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        return $this->render([
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signin')
        ]);
    }

    /**
     * ユーザー登録
     * @return false|string|void
     * @throws HttpNotFoundException
     */
    public function registerAction()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signup', $token)) {
            return $this->redirect('account/signup');
        }

        $userName = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        // バリデーション
        $errors = [];
        if (!mb_strlen($userName)) {
            $errors = [
                'user_name' => 'ユーザーIDを入力してください'
            ];
        } else if (!preg_match("/^\w{3,20}$/", $userName)) {
            $errors = [
                'user_name' => 'ユーザーIDは半角英数字およびアンダースコアを3~20文字以内で入力してください'
            ];
        } else if (!$this->dbManager->get('User')->isUniqueUserName($userName)) {
            $errors = [
                'user_name' => 'ユーザーIDは既に使用されています'
            ];
        }

        // エラーがなければユーザー登録してトップ画面へ
        if (count($errors) === 0) {
            $this->dbManager->get('User')->insert($userName, $password);
            $this->session->setAuthenticated(true);

            $user = $this->dbManager->get('User')->fetchByUserName($userName);
            $this->session->set('user', $user);

            return $this->redirect('/');
        }

        return $this->render([
            'user_name' => $userName,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signup'),
        ], 'signup');
    }

    /**
     * ログイン
     * @return false|string|void
     * @throws HttpNotFoundException
     */
    public function authenticateAction()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signin', $token)) {
            return $this->redirect('/account/signin');
        }

        $userName = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        // バリデーション
        $errors = [];
        if (!mb_strlen($userName)) {
            $errors[] = 'ユーザIDを入力してください';
        }

        if (!mb_strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors) === 0) {
            $userRepository = $this->dbManager->get('User');
            $user = $userRepository->fetchByUserName($userName);

            // 入力されたパスワードが正しいか
            if (!$user || ($user['password'] !== $userRepository->hashPassword($password))) {
                $errors[] = 'ユーザIDかパスワードが不正です';
            } else {
                $this->session->setAuthenticated(true);
                $this->session->set('user', $user);

                return $this->redirect('/');
            }
        }

        return $this->render([
            'user_name' => $userName,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signin')
        ], 'signin');
    }

    /**
     * ログアウト
     */
    public function signoutAction()
    {
        $this->session->clear();
        $this->session->setAuthenticated(false);

        return $this->redirect('/account/signin');
    }

    /**
     * アカウント画面を表示
     * @return false|string
     */
    public function indexAction()
    {
        $user = $this->session->get('user');
        $followings = $this->dbManager->get('User')
            ->fetchAllFollowingByUserId($user['id']);

        return $this->render([
            'user'       => $user,
            'followings' => $followings
        ]);
    }

    /**
     * ユーザーをフォロー
     * @throws HttpNotFoundException
     */
    public function followAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $followingName = $this->request->getPost('following_name');
        if (!$followingName) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/follow', $token)) {
            return $this->redirect("/user/{$followingName}");
        }

        $followUser = $this->dbManager->get('User')
            ->fetchByUserName($followingName);
        if (!$followUser) {
            $this->forward404();
        }

        $user = $this->session->get('user');

        $followingRepository = $this->dbManager->get('Following');
        // フォロー対象ユーザーが自分でないこと、かつ既にフォローしていないことを確認
        if ($user['id'] !== $followUser['id']
            && !$followingRepository->isFollowing($user['id'], $followUser['id'])
        ) {
            $followingRepository->insert($user['id'], $followUser['id']);
        }

        return $this->redirect('/account');
    }
}