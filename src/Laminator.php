<?php

namespace Benzine\ORM;

use Benzine\App;
use Benzine\Configuration\Configuration;
use Benzine\Configuration\Exceptions\Exception;
use Benzine\Exceptions\BenzineException;
use Benzine\ORM\Components\Model;
use Benzine\ORM\Connection\Database;
use Benzine\ORM\Connection\Databases;
use Benzine\ORM\Exception\SchemaToAdaptorException;
use Benzine\Services\ConfigurationService;
use Benzine\Twig\Extensions\ArrayUniqueTwigExtension;
use Camel\CaseTransformer;
use Camel\Format;
use DirectoryIterator;
use Gone\Twig\InflectionExtension;
use Gone\Twig\TransformExtension;
use Laminas\Db\Metadata\Object\TableObject;
use Laminas\Stdlib\ConsoleHelper;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader as TwigFileSystemLoader;

class Laminator
{
    public CaseTransformer $transSnake2Studly;
    public CaseTransformer $transStudly2Camel;
    public CaseTransformer $transStudly2Studly;
    public CaseTransformer $transCamel2Studly;
    public CaseTransformer $transSnake2Camel;
    public CaseTransformer $transSnake2Spinal;
    public CaseTransformer $transCamel2Snake;
    private string $workPath;
    private static ConfigurationService $benzineConfig;
    private array  $config = [
        'templates' => [],
        'formatting' => [],
        'sql' => [],
        'clean' => [],
    ];
    private static bool $useClassPrefixes = false;
    private TwigFileSystemLoader $loader;
    private TwigEnvironment $twig;
    private Databases $databases;
    private array $ignoredTables = [];
    private \SimpleXMLElement $coverageReport;
    private bool $waitForKeypressEnabled = true;

    private array $defaultEnvironment = [];
    private array $defaultHeaders = [];
    private int $expectedFileOwner;
    private int $expectedFileGroup;
    private int $expectedPermissions;

    public function __construct(string $workPath, ConfigurationService $benzineConfig, Databases $databases)
    {
        $this->workPath = $workPath;
        self::$benzineConfig = $benzineConfig;
        $this->databases = $databases;

        $script = realpath($_SERVER['SCRIPT_FILENAME']);
        $this->expectedFileOwner = fileowner($script);
        $this->expectedFileGroup = filegroup($script);
        $this->expectedPermissions = fileperms($script);

        set_exception_handler([$this, 'exceptionHandler']);
        $this->setUp();

        $this->defaultEnvironment = [
            'SCRIPT_NAME' => '/index.php',
            'RAND' => rand(0, 100000000),
        ];
        $this->defaultHeaders = [];
    }

    private function setUp()
    {
        $customPathsToPSR2 = [];
        if (isset($this->config['clean'], $this->config['clean']['paths'])) {
            foreach ($this->config['clean']['paths'] as $path) {
                $customPathsToPSR2[] = "/app/{$path}";
            }
        }

        if (self::$benzineConfig->has(ConfigurationService::KEY_APP_ROOT)) {
            $this->setWorkPath(self::$benzineConfig->get(ConfigurationService::KEY_APP_ROOT));
        }

        $this->loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/Generator/templates');
        $this->twig = new \Twig\Environment($this->loader, ['debug' => true]);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->twig->addExtension(new TransformExtension());
        $this->twig->addExtension(new InflectionExtension());

        $this->twig->addExtension(
            new ArrayUniqueTwigExtension()
        );

        $fct = new \Twig\TwigFunction('var_export', 'var_export');
        $this->twig->addFunction($fct);

        // Skip tables specified in configuration.
        if (isset($this->config['database'], $this->config['database']['skip_tables'])) {
            $this->ignoredTables = $this->config['database']['skip_tables'];
        }

        $this->transSnake2Studly = new CaseTransformer(new Format\SnakeCase(), new Format\StudlyCaps());
        $this->transStudly2Camel = new CaseTransformer(new Format\StudlyCaps(), new Format\CamelCase());
        $this->transStudly2Studly = new CaseTransformer(new Format\StudlyCaps(), new Format\StudlyCaps());
        $this->transCamel2Studly = new CaseTransformer(new Format\CamelCase(), new Format\StudlyCaps());
        $this->transSnake2Camel = new CaseTransformer(new Format\SnakeCase(), new Format\CamelCase());
        $this->transSnake2Spinal = new CaseTransformer(new Format\SnakeCase(), new Format\SpinalCase());
        $this->transCamel2Snake = new CaseTransformer(new Format\CamelCase(), new Format\SnakeCase());

        return $this;
    }

    public function getBenzineConfig(): ConfigurationService
    {
        return self::BenzineConfig();
    }

    public static function BenzineConfig(): ConfigurationService
    {
        return self::$benzineConfig;
    }

    public function getWorkPath(): string
    {
        return $this->workPath;
    }

    public function setWorkPath(string $workPath): self
    {
        $this->workPath = $workPath;

        return $this;
    }

    public function exceptionHandler($exception): void
    {
        // UHOH exception handler
        /** @var \Exception $exception */
        echo "\n".ConsoleHelper::COLOR_RED;
        echo " ____ ____ ____ ____ \n";
        echo "||U |||H |||O |||H ||\n";
        echo "||__|||__|||__|||__||\n";
        echo "|/__\\|/__\\|/__\\|/__\\|\n";
        echo ConsoleHelper::COLOR_RESET."\n\n";
        echo $exception->getMessage();
        echo "\n\n";
        echo "In {$exception->getFile()}:{$exception->getLine()}";
        echo "\n\n";
        echo $exception->getTraceAsString();
        echo "\n\n";

        exit(1);
    }

    public static function isUsingClassPrefixes(): bool
    {
        return self::$useClassPrefixes;
    }

    /**
     * @return \Slim\App
     */
    public function _CORE()
    {
        return App::Instance()
            ->loadAllRoutes()
            ->getApp()
        ;
    }

    /**
     * @param $schemaName
     *
     * @throws SchemaToAdaptorException
     *
     * @return int|string
     */
    public function schemaName2databaseName($schemaName)
    {
        foreach ($this->databases->getAll() as $dbName => $database) {
            if ($schemaName == $database->getAdapter()->getCurrentSchema()) {
                return $dbName;
            }
        }

        throw new SchemaToAdaptorException("Could not translate {$schemaName} to an appropriate dbName");
    }

    public function sanitiseTableName(string $tableName, Database $database)
    {
        // Take the Alias directly
        if (self::$benzineConfig->has("databases/{$database->getName()}/table_options/{$tableName}/alias")) {
            $tableName = self::$benzineConfig->get("databases/{$database->getName()}/table_options/{$tableName}/alias");
        }
        // Take the specific transformer next
        elseif (self::$benzineConfig->has("databases/{$database->getName()}/table_options/{$tableName}/transform")) {
            $transform = self::$benzineConfig->get("databases/{$database->getName()}/table_options/{$tableName}/transform");
            $tableName = $this->{$transform}->transform($tableName);
        }
        // Take the shared transformer after that
        elseif (self::$benzineConfig->has("databases/{$database->getName()}/table_options/_/transform")) {
            $transform = self::$benzineConfig->get("databases/{$database->getName()}/table_options/_/transform");
            $tableName = $this->{$transform}->transform($tableName);
        }

        // Iterate over all the replacement strings and apply them
        if (self::$benzineConfig->has("databases/{$database->getName()}/table_options/_/replace")) {
            $replacements = self::$benzineConfig->getArray("databases/{$database->getName()}/table_options/_/replace");
            foreach ($replacements as $before => $after) {
                //echo "  > Replacing {$before} with {$after} in {$tableName}\n";
                $tableName = str_replace($before, $after, $tableName);
            }
        }

        // Simply remove the prefix
        if (self::$benzineConfig->get('benzine/databases/remove_prefix')) {
            if (substr($tableName, 0, strlen(self::$benzineConfig->get('benzine/databases/remove_prefix'))) == self::$benzineConfig->get('benzine/databases/remove_prefix')) {
                $tableName = substr($tableName, 2);
            }
        }

        return $tableName;
    }

    public function getAutoincrementColumns(Database $database, $table)
    {
        switch ($database->getAdapter()->getDriver()->getDatabasePlatformName()) {
            case 'Mysql':
                $sql = "SHOW columns FROM `{$table}` WHERE extra LIKE '%auto_increment%'";
                $query = $database->getAdapter()->query($sql);
                $columns = [];

                foreach ($query->execute() as $aiColumn) {
                    $columns[] = $aiColumn['Field'];
                }

                return $columns;

            case 'Postgresql':
                $sql = "SELECT column_name FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$table}' AND column_default LIKE 'nextval(%'";
                $query = $database->getAdapter()->query($sql);
                $columns = [];

                foreach ($query->execute() as $aiColumn) {
                    $columns[] = $aiColumn['column_name'];
                }

                return $columns;

            default:
                throw new BenzineException("Don't know how to get autoincrement columns for {$database->getAdapter()->getDriver()->getDatabasePlatformName()}!");
        }
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return $this
     */
    public function makeLaminator()
    {
        $models = $this->makeModelSchemas();
        echo 'Removing core generated files ... ';
        $this->removeCoreGeneratedFiles();
        echo "[DONE]\n";

        $this->makeCoreFiles($models);

        return $this;
    }

    /**
     * @return Model[]
     */
    private function makeModelSchemas(): array
    {
        /** @var Model[] $models */
        $models = [];
        $keys = [];
        foreach ($this->databases->getAll() as $dbName => $database) {
            /** @var Database $database */
            echo "Database: {$dbName}\n";
            /** @var TableObject $tables */
            $tables = $database->getMetadata()->getTables();

            echo 'Collecting '.count($tables)." entities data.\n";

            foreach ($tables as $table) {
                if (in_array($table->getName(), $this->ignoredTables, true)) {
                    continue;
                }
                $oModel = Components\Model::Factory($this)
                    ->setClassPrefix(self::$benzineConfig->get("databases/{$dbName}/class_prefix", null))
                    ->setNamespace(self::$benzineConfig->getNamespace())
                    ->setDatabase($database)
                    ->setTable($table->getName())
                ;

                if (self::$benzineConfig->has("databases/{$dbName}/class_prefix")) {
                    $oModel->setClassPrefix(self::$benzineConfig->get("databases/{$dbName}/class_prefix"));
                }
                $models[$oModel->getClassName()] = $oModel;
                $keys[$database->getAdapter()->getCurrentSchema().'::'.$table->getName()] = $oModel->getClassName();
            }
        }
        ksort($models);
        ksort($keys);
        foreach ($this->databases->getAll() as $dbName => $database) {
            /** @var Database $database */
            $tables = $database->getMetadata()->getTables();
            foreach ($tables as $table) {
                $key = $keys[$database->getAdapter()->getCurrentSchema().'::'.$table->getName()];
                $models[$key]
                    ->computeColumns($table->getColumns())
                    ->computeConstraints($models, $keys, $table->getConstraints())
                ;
            }
        }

        ksort($models);

        // Scan for remote relations
        //\Kint::dump(array_keys($models));
        foreach ($models as $oModel) {
            $oModel->scanForRemoteRelations($models);
        }

        // Check for Conflicts.
        $conflictCheck = [];
        foreach ($models as $oModel) {
            if (count($oModel->getRemoteObjects()) > 0) {
                foreach ($oModel->getRemoteObjects() as $remoteObject) {
                    //echo "Base{$remoteObject->getLocalClass()}Model::fetch{$remoteObject->getRemoteClass()}Object\n";
                    if (!isset($conflictCheck[$remoteObject->getLocalClass()][$remoteObject->getRemoteClass()])) {
                        $conflictCheck[$remoteObject->getLocalClass()][$remoteObject->getRemoteClass()] = $remoteObject;
                    } else {
                        $conflictCheck[$remoteObject->getLocalClass()][$remoteObject->getRemoteClass()]->markClassConflict(true);
                        $remoteObject->markClassConflict(true);
                    }
                }
            }
        }

        // Bit of Diag...
        //foreach($models as $oModel){
        //    if(count($oModel->getRemoteObjects()) > 0) {
        //        foreach ($oModel->getRemoteObjects() as $remoteObject) {
        //            echo " > {$oModel->getClassName()} has {$remoteObject->getLocalClass()} on {$remoteObject->getLocalBoundColumn()}:{$remoteObject->getRemoteBoundColumn()} (Function: {$remoteObject->getLocalFunctionName()})\n";
        //        }
        //    }
        //}

        // Finally return some models.
        return $models;
    }

    private function removeCoreGeneratedFiles()
    {
        $generatedPaths = [
            'src/Controllers/Base/',
            'src/Models/Base/',
            'src/Routes/Generated/',
            'src/Services/Base/',
            'src/TableGateways/Base/',
            'tests/Api/Generated/',
            'tests/Models/Generated/',
            'tests/Services/Generated/',
            'tests/',
        ];
        foreach ($generatedPaths as $generatedPath) {
            if ((new Filesystem())->exists($generatedPath)) {
                foreach (new DirectoryIterator($generatedPath) as $file) {
                    if (!$file->isDot() && 'php' == $file->getExtension()) {
                        (new Filesystem())->remove($file->getRealPath());
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param Model[] $models
     *
     * @throws LoaderError  When the template cannot be found
     * @throws SyntaxError  When an error occurred during compilation
     * @throws RuntimeError When an error occurred during rendering
     *
     * @return Laminator
     */
    private function makeCoreFiles(array $models)
    {
        echo 'Generating Core files for '.count($models)." models... \n";
        $allModelData = [];
        foreach ($models as $model) {
            $allModelData[$model->getClassName()] = $model->getRenderDataset();
            // "Model" suite
            echo " > {$model->getClassName()}\n";

            //\Kint::dump($model->getRenderDataset());
            if (in_array('Models', $this->getBenzineConfig()->getLaminatorTemplates(), true)) {
                $this->renderToFile(true, "src/Models/Base/AbstractBase{$model->getClassName()}Model.php", 'Models/basemodel.php.twig', $model->getRenderDataset());
                $this->renderToFile(false, "src/Models/{$model->getClassName()}Model.php", 'Models/model.php.twig', $model->getRenderDataset());
                $this->renderToFile(true, "tests/Models/Generated/{$model->getClassName()}Test.php", 'Models/tests.models.php.twig', $model->getRenderDataset());
                $this->renderToFile(true, "src/TableGateways/Base/AbstractBase{$model->getClassName()}TableGateway.php", 'Models/basetable.php.twig', $model->getRenderDataset());
                $this->renderToFile(false, "src/TableGateways/{$model->getClassName()}TableGateway.php", 'Models/table.php.twig', $model->getRenderDataset());
            }

            // "Service" suite
            if (in_array('Services', $this->getBenzineConfig()->getLaminatorTemplates(), true)) {
                $this->renderToFile(true, "src/Services/Base/AbstractBase{$model->getClassName()}Service.php", 'Services/baseservice.php.twig', $model->getRenderDataset());
                $this->renderToFile(false, "src/Services/{$model->getClassName()}Service.php", 'Services/service.php.twig', $model->getRenderDataset());
                $this->renderToFile(true, "tests/Services/Generated/{$model->getClassName()}Test.php", 'Services/tests.service.php.twig', $model->getRenderDataset());
            }

            // "Controller" suite
            if (in_array('Controllers', $this->getBenzineConfig()->getLaminatorTemplates(), true)) {
                $this->renderToFile(true, "src/Controllers/Base/AbstractBase{$model->getClassName()}Controller.php", 'Controllers/basecontroller.php.twig', $model->getRenderDataset());
                $this->renderToFile(false, "src/Controllers/{$model->getClassName()}Controller.php", 'Controllers/controller.php.twig', $model->getRenderDataset());
            }

            // "Endpoint" test suite
            if (in_array('Endpoints', $this->getBenzineConfig()->getLaminatorTemplates(), true)) {
                $this->renderToFile(true, "tests/Api/Generated/{$model->getClassName()}EndpointTest.php", 'ApiEndpoints/tests.endpoints.php.twig', $model->getRenderDataset());
            }

            // "Routes" suite
            if (in_array('Routes', $this->getBenzineConfig()->getLaminatorTemplates(), true)) {
                $this->renderToFile(true, "src/Routes/Generated/{$model->getClassName()}Route.php", 'Router/route.php.twig', $model->getRenderDataset());
            }
        }

        return $this;
    }

    /**
     * @throws LoaderError  When the template cannot be found
     * @throws SyntaxError  When an error occurred during compilation
     * @throws RuntimeError When an error occurred during rendering
     */
    private function renderToFile(bool $overwrite, string $path, string $template, array $data)
    {
        $output = $this->twig->render($template, $data);
        $path = $this->getWorkPath().'/'.$path;

        if (!(new Filesystem())->exists(dirname($path))) {
            (new Filesystem())->mkdir(dirname($path), 0777);
        }
        if (!(new Filesystem())->exists($path) || $overwrite) {
            //printf(" [Done]" . PHP_EOL);
            (new Filesystem())->dumpFile($path, $output);
        }
        //printf(" [Skip]" . PHP_EOL);

        // Make permissions match the expected owners/groups/perms
        (new Filesystem())->chown($path, $this->expectedFileOwner);
        //(new Filesystem())->chgrp($path, $this->expectedFileGroup);
        (new Filesystem())->chmod($path, $this->expectedPermissions);

        return $this;
    }
}
