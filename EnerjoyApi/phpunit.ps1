param([Switch]$refresh)
if ($refresh){
    Write-Host "Migrating + seeding database"
    php artisan migrate:fresh --seed
    Write-Host "Done migrating + seeding database"
}
$date = Get-Date -Format "dd-MM-yyyy_HH-mm"
Write-Host "Starting test on $date"
./vendor/bin/phpunit --testdox-html "./logs/${date}.html"