# WP_Modules

Open Source WordPress Framework

Developers are welcome to create pull requests here.

## Supports : 
- Modules structure
- WordPress coding standards
- Namespaces
- Dependency Injection
- Autoloading
- Core dependecies via composer

## How to start
- cd ./wp-content/mu-plugins
- git clone git@github.com:kuflievskiy/WP_Modules.git ./
- cd wp-modules
- composer update
- check wp-modules-skeleton application and start coding!

## To do
- [ ] Add basic documentation 
- [ ] Add basic documentation about the sample module structure.
- [ ] Add Travis CI and enable PHP code sniffing.
- [ ] Add WP_Modules\Taxonomy class.
- [ ] Add WP_Modules\Post_Type class.
- [ ] Complete implementation for WP_Modules\Cron class.
- [ ] Write a simple script to set up test WordPress site on Travis CI
- [ ] Process automated tests on Travis CI
- [ ] Create bash script to init new sample app,module structure etc and document it in the proper way.

```bash
	bash wp-module create app new-sample-app
	bash wp-module create app-module new-sample-module
	bash wp-module create app-module-controller sample-controller
```

- [ ] Add Symfony Migration mechanism.
- [ ] Add Routing module based on WordPress rewrite rules WP_Modules_Skeleton\Route