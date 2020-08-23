<?php

class AccountController extends Controller
{
    public function signupAction()
    {
        return $this->render([
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signup'),
        ]);
    }

    public function registerAction()
    {
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

            return $this->redirect('/');
        }

        return $this->render([
            'user_name' => $userName,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signup'),
        ], 'signup');
    }
}