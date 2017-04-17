root_path="$PWD/.."

isProdMode() {
    if [ "$1" == "prod" ]; then
        return 0
    else
        return 1
    fi
}

echo \> Updating Sources
cd "$root_path"
echo "$PWD"
git pull
echo \> Done

echo \> Building Frontend Application
cd "$root_path/frontend"
echo "$PWD"
if isProdMode $1; then
    npm run build:prod
else
    npm run build
fi
echo \> Done

echo \> Migrating Database
cd "$root_path/backend"
echo "$PWD"
php artisan migrate --force
echo \> Done

echo \> Publishing Frontend Application
cd "$root_path/frontend"
echo "$PWD"
rm -r public
cp -r dist public
echo \> Done

echo \> Generating REST API Documentation
cd "$root_path/backend"
echo "$PWD"
php artisan generate:rest_api_documentation
echo \> Done

if isProdMode $1; then
    echo \> Restarting Frontend Application
    cd "$root_path/frontend/public"
    echo "$PWD"
    pm2 restart server.js
    echo \> Done
fi

if isProdMode $1; then
    echo \> Restarting Backend Application
    cd "$root_path/frontend/public"
    echo "$PWD"
    sudo systemctl restart nginx php7.0-fpm
    echo \> Done
fi