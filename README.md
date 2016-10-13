Installation
------------

### Open terminal and run composer require command

```
composer require --dev nguonchhay/nodetypegenerator dev-master
```

### Or add directly to your `composer.json` in the `require-dev` section of your directory root:

```
"require-dev": {
    "nguonchhay/nodetypegenerator": "dev-master"
}
```

Then download the package: `composer update nguonchhay/nodetypegenerator`

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

### Generating new document or content nodetype with three easy steps

```
    1. Go to generating form `<your-base-url>/nodetypegenerator` and input the nodetype information then submit the form.
    2. Review generated nodetype. You can adjust fusion(.ts2) and template(.html) base on your real requirement.
    3. Import gernerated nodetype into your active sites. You can import generated nodetype to active sites.
```

Then go to back end and test that imported nodetype.

Making change
-------------

We develop `Nguonchhay.NodeTypeGenerator` by using: 
 
```
    1. Bower: download font awesome
    2. Grunt: compile resources
    3. CoffeeScript: developing script
    4. Scss: developing style
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
