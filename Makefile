SHELL := /bin/bash
VERSION_REGEX := \"version\":\s*\"([0-9]+\.[0-9]+\.[0-9]+)\",\s*$$
VUETIFY_VERSION := $(shell cat node_modules/vuetify/package.json | sed -rn "s/${VERSION_REGEX}/\1/p" | sed -e 's/^[[:space:]]*//')

# This will allow me to maintain a versioned copy of Vuetify's CSS bundle separate
# from my own bundle, making it more cacheable (otherwise, Vuetify's CSS will
# have to be downloaded again every time my own app's bundle changes)
_rename_vuetify:
	mv public/css/vuetify.min.css public/css/vuetify${VUETIFY_VERSION}.min.css
	mv public/css/vuetify.css.map public/css/vuetify${VUETIFY_VERSION}.css.map
	sed -i '/^VUETIFY_VERSION=.*$$/ d' .env
	echo "VUETIFY_VERSION=${VUETIFY_VERSION}" >> .env

prod:
	npm --section=adminauth run prod
	npm --section=admin run prod
	nom --section=pages run prod
	npm --section=app run prod
	php artisan view:cache
	php artisan route:cache
	make _rename_vuetify

dev:
	npm --section=adminauth run dev
	npm --section=admin run dev
	npm --section=pages run dev
	npm --section=app run dev
	php artisan view:clear
	php artisan route:clear
	make _rename_vuetify

clean:
	-rm -f public/js/*.js public/js/*.map public/css/*.map public/js/*.txt 2> /dev/null
	-rm -f public/js/adminauth/*.js public/js/adminauth/*.map public/js/adminauth/*.txt 2> /dev/null
	-rm -f public/js/admin/*.js public/js/admin/*.map public/js/admin/*.txt 2> /dev/null
	-rm -f public/css/*.css 2> /dev/null
	-rm -f public/css/adminauth/*.css 2> /dev/null
	-rm -f public/css/admin/*.css 2> /dev/null
	-rm -f public/mix-manifest.json 2> /dev/null
	-rm -rf public/fonts/* 2> /dev/null
	php artisan view:clear
	php artisan route:clear
