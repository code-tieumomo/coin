npx concurrently -c "red,green,blue,blue,blue" -n "dev,reverb,queue1,queue2,queue3,queue4,queue5" \
  "npm run dev" \
  "php artisan reverb:start" \
  "php artisan queue:work" \
  "php artisan queue:work" \
  "php artisan queue:work" \
  "php artisan queue:work" \
  "php artisan queue:work"
