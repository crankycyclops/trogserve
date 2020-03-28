prod:
	npm --section=adminauth run prod
	npm --section=admin run prod
	npm --section=app run prod
	php artisan view:cache
	php artisan route:cache

dev:
	npm --section=adminauth run dev
	npm --section=admin run dev
	npm --section=app run dev
	php artisan view:clear
	php artisan route:clear

clean:
	-rm -f public/js/*.js public/js/*.map public/js/*.txt 2> /dev/null
	-rm -f public/js/adminauth/*.js public/js/adminauth/*.map public/js/adminauth/*.txt 2> /dev/null
	-rm -f public/js/admin/*.js public/js/admin/*.map public/js/admin/*.txt 2> /dev/null
	-rm -f public/css/*.css 2> /dev/null
	-rm -f public/css/adminauth/*.css 2> /dev/null
	-rm -f public/css/admin/*.css 2> /dev/null
	-rm -f public/mix-manifest.json 2> /dev/null
	php artisan view:clear
	php artisan route:clear
