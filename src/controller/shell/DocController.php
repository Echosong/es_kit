<?php
/**
 * Created by PhpStorm.
 * User: echosong
 * Date: 2017/9/11
 * Time: 11:05
 */

use Crada\Apidoc\Builder;
use Crada\Apidoc\Exception;

class DocController extends BaseController
{
    /**
     * 生成帮助文档脚本 php index.php shell doc apidoc
     */
    public function actionApiDoc()
    {

        spl_autoload_register(function ($class) {
            $file = APP_DIR . '/src/controller/vip/' . $class . '.php';
            if (file_exists($file)) {
                include $file;
            }
        });
        $__controller = $_REQUEST['p'] . "Controller";
        $output_dir = APP_DIR . '/apidocs';
        $output_file = $__controller . '.html';

        try {
            $builder = new Builder([$__controller], $output_dir, $__controller . 'Title', $output_file);
            $builder->generate();
            echo $__controller . ' success';
        } catch (Exception $e) {
            echo 'There was an error generating the documentation: ', $e->getMessage();
        }
    }

    /**
     * 站点发布脚本 php index.php shell doc release
     */
    public function actionRelease()
    {
        define("DEFAULT_PATH", "H:/release/yxbd");
        define("DEFAULT_APP", "web");

        fwrite(STDOUT, '请输入要发布的位置（默认为' . DEFAULT_PATH . '）：');
        $path = fgets(STDIN);
        if (strlen($path) < 3) {
            $path = DEFAULT_PATH;
        }
        fwrite(STDOUT, '请输入要发布的项目（默认为 ' . DEFAULT_APP . '）：');
        $m = trim(fgets(STDIN));
        if (strlen($m) < 3) {
            $m = DEFAULT_APP;
        }
        $fileSystem = new Symfony\Component\Filesystem\Filesystem();
        if($fileSystem->exists([$path.'/'.$m."/"])){
            $fileSystem->remove([$path.'/'.$m."/"]);
        }
        echo "clear \r\n";
        $fileSystem->mkdir($path);
        $fileSystem->copy(APP_DIR . "/index.php", $path . "/" . $m . "/index.php");
        $fileSystem->mirror(APP_DIR . "/vendor", $path . "/" . $m . "/vendor");
        $fileSystem->mirror(APP_DIR . "/src/controller/" . $m , $path . "/" . $m . "/src/controller/" . $m );
        $fileSystem->mirror(APP_DIR . "/src/view/" . $m , $path . "/" . $m . "/src/view/" . $m );
        $fileSystem->mirror(APP_DIR . "/src/core", $path . "/" . $m . "/src/core");
        $fileSystem->mirror(APP_DIR . "/src/plugin", $path . "/" . $m . "/src/plugin");
        $fileSystem->mirror(APP_DIR . "/src/model", $path . "/" . $m . "/src/model");
        $fileSystem->mirror(APP_DIR . "/res" , $path . "/" . $m . "/res");
        echo "published";
    }
}