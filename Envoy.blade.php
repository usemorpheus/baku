@servers(['web' => 'forge@34.124.149.53'])

@task('deploy')
cd /home/forge/baku
php artisan clear
git pull
composer install --no-dev
@endtask
