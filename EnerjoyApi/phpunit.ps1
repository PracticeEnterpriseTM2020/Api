$date = Get-Date -Format "dd-MM-yyyy_HH-mm"
./vendor/bin/phpunit --testdox-html "./logs/${date}.html"