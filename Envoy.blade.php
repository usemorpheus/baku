@servers(['web' => 'parse'])

@task('deploy')
cd baku
git submodule update --recursive --remote
git pull
composer install
sudo service php8.3-fpm reload
sudo supervisorctl reload
@endtask
