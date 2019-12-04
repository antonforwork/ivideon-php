call "vendor/bin/lazydoctor" -c=composer.json -f=true -p=true
call php-cs-fixer fix --config=.php_cs.dist --allow-risky yes
