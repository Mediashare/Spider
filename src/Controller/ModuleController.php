<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use App\Service\Module;

class ModuleController extends AbstractController
{   
    public function __construct(Module $module) {
        $this->module = new Module();
    }

    /**
     * @Route("/modules", name="module")
     */
    public function index() {
        $modulesDir = $this->getParameter('modules_dir');
        $modules = $this->module->getModules($modulesDir);
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }
    
    /**
     * @Route("/module/new", name="module_new")
     */
    public function new(Request $request) {
        $boilerplate = '<?php public function run() { return "Hello Webspider!"; } ?>';
        // Check if form is submited
        $module['name'] = $request->request->get('name');
        if ($module['name']) {
            $module['description'] = $request->request->get('description');
            $module['variablesInjection'] = json_encode($request->request->get('variablesInjection'));
            $module['function'] = $request->request->get('function');
            $module = $this->createBoilerplate($module);
            $boilerplate = $module['function'];
            return $this->redirect($this->generateUrl('module_show', ['name' => $module['name']]));
        }
        return $this->render('module/new.html.twig', [
            'boilerplate' => $boilerplate,
        ]);
    }


    /**
     * @Route("/module/show/{name}", name="module_show")
     */
    public function show(string $name) {
        $module = $this->module->getModule($name);
        $boilerplate = file_get_contents($modulesDir.$name.'.php');

        return $this->render('module/show.html.twig', [
            'module' => $module,
            'boilerplate' => $boilerplate,
            'readOnly' => true
        ]);
    }


    // *********************** 
    // Boilerplate functions *
    // ***********************
    private function createBoilerplate(array $module) {
        $module = $this->config($module);
        $boilerplate = $this->getBoilerplate();
        $boilerplate = $this->rewriteBoilerplate($module, $boilerplate);
        $module = $this->createFile($module, $boilerplate);
        return $module;
    }

    private function getBoilerplate() {
        $modulesDir = $this->getParameter('modules_dir');
        $boilerplate = $modulesDir.'../Boilerplate/module.bp';
        $boilerplate = file_get_contents($boilerplate);
        
        return $boilerplate;
    }

    private function config(array $module) {
        $modulesDir = $this->getParameter('modules_dir');
        $name = $module['name'];
        // Check if Module exist already
        $className = ucfirst(preg_replace( '/[^A-Za-z0-9]+/', '', $name));
        $moduleFile = $modulesDir.$className.'.php';

        $filesystem = new Filesystem();
        $moduleExist = $filesystem->exists($moduleFile);
        if ($moduleExist) {
            $className = ucfirst(preg_replace( '/[^A-Za-z0-9]+/', '', $name.uniqid()));
            $moduleFile = $modulesDir.$className.'.php';
        }
        return [
            'name' => $className,
            'className' => $className,
            'moduleFile' => $moduleFile,
            'variables' => $module['variablesInjection'],
            'description' => $module['description'],
            'function' => $module['function']
        ];
    } 
    
    private function rewriteBoilerplate(array $module, string $boilerplate) {
        $boilerplate = str_replace('%$className%', $module['className'], $boilerplate);
        $boilerplate = str_replace('%$name%', $module['className'], $boilerplate);
        $boilerplate = str_replace('%$variables%', $module['variables'], $boilerplate);
        $boilerplate = str_replace('%$description%', $module['description'], $boilerplate);
        $function = rtrim(ltrim($module['function']));
        $function = rtrim(ltrim($function,'<?php'), 'php?>');
        $function = rtrim(ltrim($function,'<?php'), '?>');
        $boilerplate = str_replace('%$function%', $function, $boilerplate);
        
        return $boilerplate;
    }

    private function createFile(array $module, string $boilerplate) {
        $filesystem = new Filesystem();
        try {
            $filesystem->dumpFile($module['moduleFile'], $boilerplate);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your module file at ".$exception->getPath();
        }
        return $module;
    }
}
