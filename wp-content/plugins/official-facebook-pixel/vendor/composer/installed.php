<?php return array(
    'root' => array(
        'name' => 'facebook/pixel-for-wordpress',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => 'c7e3e747cb6b924ef3c8c4dfb03862b2ae30873d',
        'type' => 'project',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'facebook/php-business-sdk' => array(
            'pretty_version' => '22.0.0',
            'version' => '22.0.0.0',
            'reference' => 'c83b450089c557a1c64960103f3c658a429bb525',
            'type' => 'library',
            'install_path' => __DIR__ . '/../facebook/php-business-sdk',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'facebook/pixel-for-wordpress' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'c7e3e747cb6b924ef3c8c4dfb03862b2ae30873d',
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'techcrunch/wp-async-task' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '9bdbbf9df4ff5179711bb58b9a2451296f6753dc',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../techcrunch/wp-async-task',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
    ),
);
