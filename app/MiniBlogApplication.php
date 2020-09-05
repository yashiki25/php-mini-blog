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
            '/'                           => [
                'controller' => 'status',
                'action'     => 'index',
            ],
            '/status/post'                => [
                'controller' => 'status',
                'action'     => 'post',
            ],
            '/account'                    => [
                'controller' => 'account',
                'action'     => 'index',
            ],
            '/account/:action'            => [
                'controller' => 'account',
            ],
            '/user/:user_name'            => [
                'controller' => 'status',
                'action'     => 'user',
            ],
            '/user/:user_name/status/:id' => [
                'controller' => 'status',
                'action'     => 'show',
            ]
        ];
    }

    protected function configure()
    {
        $this->dbManager->connect('master', [
            'dsn'      => 'mysql:dbname=' . Env::get('DB_DATABASE') . ';host=' . Env::get('DB_HOST'),
            'user'     => Env::get('DB_USERNAME'),
            'password' => Env::get('DB_PASSWORD'),
        ]);
    }
}