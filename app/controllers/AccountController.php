<?php

class AccountController extends Controller
{
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

    public function signoutAction()
    {
        $this->session->clear();
        $this->session->setAuthenticated(false);

        return $this->redirect('/account/signin');
    }

    public function indexAction()
    {
        $user = $this->session->get('user');

        return $this->render(['user' => $user]);
    }
}