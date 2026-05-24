<?php

return [

    // Libera apenas rotas da API
    'paths' => ['api/*'],

    // Permite todos os métodos HTTP
    'allowed_methods' => ['*'],

    // Libera acesso para o site do professor
    'allowed_origins' => ['https://termorest.conradosal.com'],

    'allowed_origins_patterns' => [],

    // Permite todos os headers
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Não utiliza autenticação com cookies
    'supports_credentials' => false,

];
