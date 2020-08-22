<?php

use App\Env;

class MiniBlogApplication extends Application
{
    private array $loginAction = [
        'account',
        'signIn',
    ];

    public function getRootDir(): string
    {
        return dirname(__FILE__);
    }

    protected function registerRoutes()
    {
        return [
            '/account'         => [
                'controller' => 'account',
                'action'     => 'index',
            ],
            '/account/:action' => [
                'controller' => 'account',
            ],
        ];
    }

    private function configure()
    {
        $this->dbManager->connect('master', [
            'dsn'      => 'mysql:dbname=' . Env::get('DB_DATABASE') . ';host=' . Env::get('DB_HOST'),
            'user'     => Env::get('DB_USERNAME'),
            'password' => Env::get('DB_PASSWORD'),
        ]);
    }
}