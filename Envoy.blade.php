@servers(['web' => 'forge@34.124.149.53'])

@task('deploy')
cd /home/forge/baku
git pull
composer install
@endtask
