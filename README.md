Installation
------------

### Add the two required packages to your `composer.json` in the `require-dev` section:

```
"require-dev": {
    "nguonchhay/nodetypegenerator": "dev-master"
}
```

### Download the package

```
composer install
```

### Clear caches just to be safe

```
./flow flow:cache:flush --force
```

### Add these configuration to global route at `Configuration/Routes.yaml`

```
##
# Nguonchhay.NodeTypeGenerator route

-
  name: 'Nguonchhay NodeTypeGenerator'
  uriPattern: 'nodetypegenerator/<NguonchhayNodeTypeGeneratorSubroutes>'
  defaults:
    '@package': 'Nguonchhay.NodeTypeGenerator'
    '@format': 'html'
  subRoutes:
    NguonchhayNodeTypeGeneratorSubroutes:
      package: 'Nguonchhay.NodeTypeGenerator'

```

Making change
-------------

We develop `Nguonchhay.NodeTypeGenerator` by using: 
 
```
    1. AmindLTE theme: for style
    2. Bower: download AdminLTE theme
    3. Grunt: compile resources
    4. CoffeeScript: developing script
    5. Scss: developing style
```

### Install js dependencies and AdminLTE theme using `bower`

```
bower install
```

### Install grunt dependencies using `npm`

```
npm install
```

### Compile resources

```
grunt build
```
