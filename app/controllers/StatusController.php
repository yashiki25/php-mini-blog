<?php

class StatusController extends Controller
{
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
}