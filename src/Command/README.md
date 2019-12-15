# Commands
## WebSpider
#### Run
```bash
php bin/console spider:run http://exemple.com -w
```
#### Php - Allowed memory size
```bash
php -d memory_limit=3000M bin/console spider:run http://exemple.com -w # Php memory limit
```
## Module
#### Module list
```bash
php bin/console webspider:module:list
```
#### Disable all modules
```bash
php bin/console spider:run http://exemple.com -w -m
```
#### Enable specific modules
```bash
php bin/console spider:run http://exemple.com -w -m Links -m Search -m NewModule
```
#### Inject variables in module
```bash
php bin/console spider:run http://exemple.com -i '{"Search":{"value search"}}' -i '{"Search":{"value search 2"}}'
```
#### Creation module
```
php bin/console webspider:module:create "Module Name"
```
[Module Documentation](src/Modules/)

## Helper
```bash
bin/console spider:run -h
______________________________________________
|                                             |
|                  WebSpider                  |
|                   -------                   |
|_____________________________________________|
                                   | by Slote |
                                   |__________/

Description:
  Execute Web Crawler

Usage:
  spider:run [options] [--] <url>...

Arguments:
  url                                      Website url

Options:
  -w, --webspider                          If you want crawl all pages on this website
  -R, --require[=REQUIRE]                  Add path require. (-R foo -R bar) (multiple values allowed)
  -E, --exception[=EXCEPTION]              Add exception. If url contains one of these words then not crawled. (-E foo -E bar) (multiple values allowed)
  -j, --json                               Return json response in terminal
  -o, --output=OUTPUT                      Output path destination
  -m, --modules[=MODULES]                  Enable specific module(s). If null disable all modules (multiple values allowed)
  -i, --inject-variable[=INJECT-VARIABLE]  Inject input variables in specific module. (-i '{"moduleName":{"foo":"bar"}}') (multiple values allowed)
  -h, --help                               Display this help message
  -q, --quiet                              Do not output any message
  -V, --version                            Display this application version
      --ansi                               Force ANSI output
      --no-ansi                            Disable ANSI output
  -n, --no-interaction                     Do not ask any interactive question
  -e, --env=ENV                            The Environment name. [default: "dev"]
      --no-debug                           Switches off debug mode.
  -v|vv|vvv, --verbose                     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  This command crawl website pages.
```