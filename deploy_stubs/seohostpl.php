<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', '{{APPLICATION_NAME}}');
set('repository', '{{REPOSITORY}}');

add('shared_files', ['database/database.sqlite']);

// Hosts

host('{{HOST_NAME}}')
    ->set('port', '57185')
    ->set('remote_user', '{{HOST_USER}}')
    ->set('identifyFile', '{{SSH_PRIVATE_KEY}}')
    ->set('http_user', '{{HOST_USER}}')
    ->set('deploy_path', '/home/{{HOST_USER}}/domains/{{DOMAIN}}/public_html')
    ->set('bin/php', '/opt/alt/php82/usr/bin/php')
    ->set('bin/npm', '/opt/alt/alt-nodejs20/root/bin/npm')
    ->set('bin/node', '/opt/alt/alt-nodejs20/root/usr/bin/node');

// Tasks

task('npm:build', function () {
    run('cd {{release_path}} && {{bin/npm}} install');
    run('cd {{release_path}} && {{bin/node}} ./node_modules/.bin/vite build');
    run('cd {{release_path}} && {{bin/npm}} install --omit=dev');
});

// Hooks

after('deploy:update_code', 'npm:build');
after('deploy:failed', 'deploy:unlock');

// Queues
after('deploy:success', 'artisan:queue:restart');
after('rollback', 'artisan:queue:restart');
