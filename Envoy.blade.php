@servers(['web' => 'parse'])

@task('deploy')
cd baku
git pull
composer install --no-dev
sudo service php8.3-fpm reload
sudo supervisorctl reload
@endtask
