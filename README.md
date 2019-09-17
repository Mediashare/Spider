# Spider
:dizzy: Spider is a php command line tool that allows you to crawl a website for informations scraping.

Spider is a crawler of website modulable write in Php.
The tool allows you to retrieve information and execute code on website pages. It can be useful for SEO or security audit purposes.
Users have the possibility to use the modules created by the community or to create their own modules (written in Php via a web interface).

### What is a Crawler?
A crawler is an indexing robot, it automatically explores the pages of a website.
Using a crawler can have several interests:
- Information search & retrieval
- Validation of the SEO of your website
- Integration test
- Execution of PHP code on several pages in an automated way

### Features
  - Get all links from website
  - Check HTTP response
  - Create your own [**Modules**](src/Modules/) (Crawl & execute your php code)
  - No database, Pure PHP & Symfony
  - Output json file
#### Soon features
  - Sitemap managment

I would be happy to receive your ideas and contributions to the project :smiley:

## Getting start
### Installation
#### Github Installation
```bash
git clone https://github.com/Mediashare/Spider
cd Spider
composer install
```
#### Docker Installation
```bash
docker pull slote/spider
docker run slote/spider bin/console spider:run https://exemple.com -w
```
### Run
```bash
php bin/console spider:run http://exemple.com -w
```
##### Php - Allowed memory size
```bash
php -d memory_limit=3000M bin/console spider:run http://exemple.com -w # Php memory limit
```
[Commands](src/Command/)
## Module
Modules are tools created by the community to add features when crawling a website.
Adding a module to a crawler allows the automation of code execution on one or more pages of a website. Modules are executed when crawling a page.
### Commands
#### Module list
```bash
php bin/console spider:module:list
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
php bin/console spider:module:create "Module Name"
```
[Commands](src/Command/) | [Module Documentation](src/Modules/)

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
      --id=ID                              Id Report
  -m, --modules[=MODULES]                  Enable specific module(s). If null disable all modules (multiple values allowed)
  -i, --inject-variable[=INJECT-VARIABLE]  Inject input variables in specific module. (-i '{"moduleName":["foo","bar"]}') (multiple values allowed)
      --html                               Html output
  -h, --help                               Display this help message
  -q, --quiet                              Do not output any message
  -V, --version                            Display this application version
      --ansi                               Force ANSI output
      --no-ansi                            Disable ANSI output
  -n, --no-interaction                     Do not ask any interactive question
  -e, --env=ENV                            The Environment name. [default: "prod"]
      --no-debug                           Switches off debug mode.
  -v|vv|vvv, --verbose                     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  This command crawl website pages.
```