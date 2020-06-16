<?php

namespace Benzine\ORM;

use Benzine\ORM\Connection\Database;
use Benzine\ORM\Connection\Databases;
use Benzine\Services\ConfigurationService;
use Camel\CaseTransformer;
use Camel\Format;
use DirectoryIterator;
use Gone\Twig\InflectionExtension;
use Gone\Twig\TransformExtension;
use GuzzleHttp\Client;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Adapter as DbAdaptor;
use Laminas\Db\Metadata\Metadata;
use Laminas\Stdlib\ConsoleHelper;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Benzine\Configuration\Configuration;
use Benzine\Configuration\DatabaseConfig as DbConfig;
use Benzine\Configuration\Exceptions\Exception;
use Benzine\ORM\Components\Model;
use Benzine\ORM\Exception\SchemaToAdaptorException;
use Benzine\ORM\Twig\Extensions\ArrayUniqueTwigExtension;

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
    private \Twig\Loader\FilesystemLoader $loader;
    private \Twig\Environment $twig;
    private Databases $databases;
    private array $ignoredTables = [];
    private \SimpleXMLElement $coverageReport;
    private bool $waitForKeypressEnabled = true;
    private array $pathsToPSR2 = [
        'src/Controllers/Base',
        'src/Controllers',
        'src/Models/Base',
        'src/Models',
        'src/Routes',
        'src/Services/Base',
        'src/Services',
        'src/TableGateways/Base',
        'src/TableGateways',
        'src/*.php',
        'tests/Api',
        'tests/Controllers',
        'tests/Models',
        'tests/Services',
        'public/index.php',
    ];
    private array $phpCsFixerRules = [
        '@PSR2' => true,
        'braces' => true,
        'class_definition' => true,
        'elseif' => true,
        'function_declaration' => true,
        'array_indentation' => true,
        'blank_line_after_namespace' => true,
        'lowercase_constants' => true,
        'lowercase_keywords' => true,
        'method_argument_space' => true,
        'no_trailing_whitespace_in_comment' => true,
        'no_closing_tag' => true,
        'no_php4_constructor' => true,
        'single_line_after_imports' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        'visibility_required' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'ordered_imports' => true,
        'ordered_class_elements' => true,
        'array_syntax' => ['syntax' => 'short'],
        'phpdoc_order' => true,
        'phpdoc_trim' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
    ];

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

        $this->pathsToPSR2 = array_merge($this->pathsToPSR2, $customPathsToPSR2);

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

        $this->databases = self::$benzineConfig->getDatabases();

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

    public function exceptionHandler($exception)
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
    public function getApp()
    {
        $instanceClass = APP_CORE_NAME;

        return $instanceClass::Instance()
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
    public static function schemaName2databaseName($schemaName)
    {
        foreach (self::$benzineConfig->getDatabases()->__toArray() as $dbName => $databaseConfig) {
            $adapter = new DbAdaptor($databaseConfig);
            if ($schemaName == $adapter->getCurrentSchema()) {
                return $dbName;
            }
        }

        throw new SchemaToAdaptorException("Could not translate {$schemaName} to an appropriate dbName");
    }

    public function sanitiseTableName($tableName, $database = 'default')
    {
        // Take the Alias directly
        if (self::$benzineConfig->has("benzine/databases/{$database}/table_options/{$tableName}/alias")) {
            $tableName = self::$benzineConfig->get("benzine/databases/{$database}/table_options/{$tableName}/alias");
        }
        // Take the specific transformer next
        elseif (self::$benzineConfig->has("benzine/databases/{$database}/table_options/{$tableName}/transform")) {
            $transform = self::$benzineConfig->get("benzine/databases/{$database}/table_options/{$tableName}/transform");
            $tableName = $this->{$transform}->transform($tableName);
        }
        // Take the shared transformer after that
        elseif (self::$benzineConfig->has("benzine/databases/{$database}/table_options/_/transform")) {
            $transform = self::$benzineConfig->get("benzine/databases/{$database}/table_options/_/transform");
            $tableName = $this->{$transform}->transform($tableName);
        }

        // Iterate over all the replacement strings and apply them
        if (self::$benzineConfig->has("benzine/databases/{$database}/table_options/_/replace")) {
            $replacements = self::$benzineConfig->getArray("benzine/databases/{$database}/table_options/_/replace");
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

    public static function getAutoincrementColumns(DbAdaptor $adapter, $table)
    {
        switch ($adapter->getDriver()->getDatabasePlatformName()) {
            case 'Mysql':
                $sql = "SHOW columns FROM `{$table}` WHERE extra LIKE '%auto_increment%'";
                $query = $adapter->query($sql);
                $columns = [];

                foreach ($query->execute() as $aiColumn) {
                    $columns[] = $aiColumn['Field'];
                }

                return $columns;
            case 'Postgresql':
                $sql = "SELECT column_name FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$table}' AND column_default LIKE 'nextval(%'";
                $query = $adapter->query($sql);
                $columns = [];

                foreach ($query->execute() as $aiColumn) {
                    $columns[] = $aiColumn['column_name'];
                }

                return $columns;
            default:
                throw new Exception("Don't know how to get autoincrement columns for {$adapter->getDriver()->getDatabasePlatformName()}!");
        }
    }

    /**
     * @param bool $cleanByDefault
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return $this
     */
    public function makeLaminator($cleanByDefault = false)
    {
        $models = $this->makeModelSchemas();
        echo 'Removing core generated files...';
        $this->removeCoreGeneratedFiles();
        echo "[DONE]\n";

        $this->makeCoreFiles($models);
        if ($cleanByDefault) {
            $this->cleanCode();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function cleanCode()
    {
        if (is_array($this->config['formatting']) && in_array('clean', $this->config['formatting'], true)) {
            $this->cleanCodePHPCSFixer();
        }
        $this->cleanCodeComposerAutoloader();

        return $this;
    }

    /**
     * @return $this
     */
    public function cleanCodePHPCSFixer(array $pathsToPSR2 = [])
    {
        $begin = microtime(true);
        echo "Cleaning... \n";

        if (empty($pathsToPSR2)) {
            $pathsToPSR2 = $this->pathsToPSR2;
        }
        foreach ($pathsToPSR2 as $pathToPSR2) {
            echo " > {$pathToPSR2} ... ";
            if (file_exists($pathToPSR2)) {
                $this->cleanCodePHPCSFixer_FixFile($pathToPSR2, $this->phpCsFixerRules);
            } else {
                echo ' ['.ConsoleHelper::COLOR_RED.'Skipping'.ConsoleHelper::COLOR_RESET.", files or directory does not exist.]\n";
            }
        }

        $time = microtime(true) - $begin;
        echo ' [Complete in '.number_format($time, 2)."]\n";

        return $this;
    }

    public function cleanCodeComposerAutoloader()
    {
        $begin = microtime(true);
        echo "Optimising Composer Autoloader... \n";
        exec('composer dump-autoload -o');
        $time = microtime(true) - $begin;
        echo "\n[Complete in ".number_format($time, 2)."]\n";

        return $this;
    }

    public function runTests(
        bool $withCoverage = false,
        bool $haltOnError = false,
        string $testSuite = '',
        bool $debug = false
    ): int {
        echo "Running phpunit... \n";

        if ($withCoverage && file_exists('build/clover.xml')) {
            $previousCoverageReport = require 'build/coverage_report.php';
            $previousCoverage = floatval((100 / $previousCoverageReport->getReport()->getNumExecutableLines()) * $previousCoverageReport->getReport()->getNumExecutedLines());
        }

        $phpunitCommand = ''.
            './vendor/bin/phpunit '.
            ($withCoverage ? '--coverage-php=build/coverage_report.php --coverage-text' : '--no-coverage').' '.
            ($haltOnError ? '--stop-on-failure --stop-on-error --stop-on-warning' : '').' '.
            ($testSuite ? "--testsuite={$testSuite}" : '').' '.
            ($debug ? '--debug' : '')
        ;
        echo " > {$phpunitCommand}\n\n";
        $startTime = microtime(true);
        passthru($phpunitCommand, $returnCode);
        $executionTimeTotal = microtime(true) - $startTime;

        if ($withCoverage) {
            /** @var CodeCoverage $coverageReport */
            $coverageReport = require 'build/coverage_report.php';
            $coverage = floatval((100 / $coverageReport->getReport()->getNumExecutableLines()) * $coverageReport->getReport()->getNumExecutedLines());

            printf(
                "\nComplete in %s seconds. ",
                number_format($executionTimeTotal, 2)
            );

            printf(
                "\nCoverage: There is %s%% coverage. ",
                number_format($coverage, 2)
            );

            if (isset($previousCoverage)) {
                if ($coverage != $previousCoverage) {
                    printf(
                        'This is a %s%% %s in coverage.',
                        number_format($previousCoverage - $coverage, 2),
                        $coverage > $previousCoverage ? 'increase' : 'decrease'
                    );
                } else {
                    echo 'There is no change in coverage. ';
                }
            }
            echo "\n\n";
        }

        return $returnCode;
    }

    /**
     * @param $outputPath
     * @param bool $remoteApiUri
     * @param bool $cleanByDefault
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return $this
     */
    public function makeSDK($outputPath = APP_ROOT, $remoteApiUri = false, $cleanByDefault = true)
    {
        $this->makeSDKFiles($outputPath, $remoteApiUri);
        $this->removePHPVCRCassettes($outputPath);
        if ($cleanByDefault) {
            $this->cleanCode();
        }

        return $this;
    }

    /**
     * @param string $waitMessage
     *
     * @return bool|string
     */
    public function waitForKeypress($waitMessage = 'Press ENTER key to continue.')
    {
        if ($this->waitForKeypressEnabled) {
            echo "\n{$waitMessage}\n";

            return trim(fgets(fopen('php://stdin', 'r')));
        }

        return false;
    }

    /**
     * @param $path
     *
     * @return $this
     */
    public function purgeSDK($path)
    {
        $preserveVendor = false;
        if (file_exists("{$path}/vendor")) {
            $preserveVendor = true;
            echo "Preserving vendor directory...\n";
            $this->runScript(null, "mv {$path}/vendor /tmp/vendorbak_".date('Y-m-d_H-i-s', APP_START));
        }

        echo "Purging SDK:\n";
        $this->runScript(null, "rm -R {$path}; mkdir -p {$path}");

        if ($preserveVendor) {
            echo "Restoring vendor directory...\n";
            $this->runScript(null, 'mv /tmp/vendorbak_'.date('Y-m-d_H-i-s', APP_START)." {$path}/vendor");
        }

        return $this;
    }

    public function runSDKTests($path)
    {
        echo "Installing composer dependencies\n";
        $this->runScript($path, 'composer install');

        echo "Removing stale test cache data\n";
        $this->runScript($path, "rm -f {$path}/tests/fixtures/*.cassette");

        echo "Running tests...\n";
        $testResults = $this->runScript($path, 'API_HOST=api ./vendor/bin/phpunit --coverage-xml build/phpunit_coverage');
        if (false !== stripos($testResults, 'ERRORS!') || false !== stripos($testResults, 'FAILURES!')) {
            throw new \Exception('PHPUnit says Errors happened. Something is busted!');
        }

        if (file_exists("{$path}/build/phpunit_coverage/index.xml")) {
            $this->coverageReport = simplexml_load_file("{$path}/build/phpunit_coverage/index.xml");
        }

        echo "Tests run complete\n\n\n";

        return $this;
    }

    public function checkGitSDK($path)
    {
        if (isset($this->config['sdk']['output']['git']['repo'])) {
            echo "Preparing SDK Git:\n";
            $this->runScript(null, 'ssh-keyscan -H github.com >> /root/.ssh/known_hosts');
            $this->runScript($path, 'git init');
            $this->runScript($path, 'git remote add origin '.$this->config['sdk']['output']['git']['repo']);
            $this->runScript($path, 'git fetch --all');
            $this->runScript($path, 'git checkout master');
            $this->runScript($path, 'git pull origin master');
        } else {
            echo "Skipping GIT step, not configured in Laminator.yml: (sdk->output->git->repo)\n";
        }

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
        if (is_array($this->adapters)) {
            foreach ($this->adapters as $dbName => $adapter) {
                echo "Adaptor: {$dbName}\n";
                /**
                 * @var \Zend\Db\Metadata\Object\TableObject[]
                 */
                $tables = $this->metadatas[$dbName]->getTables();

                echo 'Collecting '.count($tables)." entities data.\n";

                foreach ($tables as $table) {
                    if (in_array($table->getName(), $this->ignoredTables, true)) {
                        continue;
                    }
                    $oModel = Components\Model::Factory($this)
                        ->setClassPrefix(self::$benzineConfig->get("benzine/databases/{$dbName}/class_prefix", null))
                        ->setNamespace(self::$benzineConfig->getNamespace())
                        ->setAdaptor($adapter)
                        ->setDatabase($dbName)
                        ->setTable($table->getName())
                    ;

                    if (self::$benzineConfig->has("benzine/databases/{$dbName}/class_prefix")) {
                        $oModel->setClassPrefix(self::$benzineConfig->get("benzine/databases/{$dbName}/class_prefix"));
                    }
                    $models[$oModel->getClassName()] = $oModel;
                    $keys[$adapter->getCurrentSchema().'::'.$table->getName()] = $oModel->getClassName();
                }
            }
            ksort($models);
            ksort($keys);
            foreach ($this->adapters as $dbName => $adapter) {
                $tables = $this->metadatas[$dbName]->getTables();
                foreach ($tables as $table) {
                    $key = $keys[$adapter->getCurrentSchema().'::'.$table->getName()];
                    $models[$key]
                        ->computeColumns($table->getColumns())
                        ->computeConstraints($models, $keys, $table->getConstraints())
                    ;
                }
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
            if (file_exists($generatedPath)) {
                foreach (new DirectoryIterator($generatedPath) as $file) {
                    if (!$file->isDot() && 'php' == $file->getExtension()) {
                        unlink($file->getRealPath());
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
                $this->renderToFile(true, "src/Models/Base/Base{$model->getClassName()}Model.php", 'Models/basemodel.php.twig', $model->getRenderDataset());
                $this->renderToFile(false, "src/Models/{$model->getClassName()}Model.php", 'Models/model.php.twig', $model->getRenderDataset());
                $this->renderToFile(true, "tests/Models/Generated/{$model->getClassName()}Test.php", 'Models/tests.models.php.twig', $model->getRenderDataset());
                $this->renderToFile(true, "src/TableGateways/Base/Base{$model->getClassName()}TableGateway.php", 'Models/basetable.php.twig', $model->getRenderDataset());
                $this->renderToFile(false, "src/TableGateways/{$model->getClassName()}TableGateway.php", 'Models/table.php.twig', $model->getRenderDataset());
            }

            // "Service" suite
            if (in_array('Services', $this->getBenzineConfig()->getLaminatorTemplates(), true)) {
                $this->renderToFile(true, "src/Services/Base/Base{$model->getClassName()}Service.php", 'Services/baseservice.php.twig', $model->getRenderDataset());
                $this->renderToFile(false, "src/Services/{$model->getClassName()}Service.php", 'Services/service.php.twig', $model->getRenderDataset());
                $this->renderToFile(true, "tests/Services/Generated/{$model->getClassName()}Test.php", 'Services/tests.service.php.twig', $model->getRenderDataset());
            }

            // "Controller" suite
            if (in_array('Controllers', $this->getBenzineConfig()->getLaminatorTemplates(), true)) {
                $this->renderToFile(true, "src/Controllers/Base/Base{$model->getClassName()}Controller.php", 'Controllers/basecontroller.php.twig', $model->getRenderDataset());
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
        //printf("  > Writing %d bytes to %s", strlen($output), $path);
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        if (!file_exists($path) || $overwrite) {
            //printf(" [Done]" . PHP_EOL);
            file_put_contents($path, $output);
        }
        //printf(" [Skip]" . PHP_EOL);

        // Make permissions match the expected owners/groups/perms
        chown($path, $this->expectedFileOwner);
        chgrp($path, $this->expectedFileGroup);
        chmod($path, $this->expectedPermissions);

        return $this;
    }

    private function removePHPVCRCassettes($outputPath)
    {
        if (file_exists($outputPath.'/tests/fixtures')) {
            $cassettesDir = new DirectoryIterator($outputPath.'/tests/fixtures/');
            foreach ($cassettesDir as $cassette) {
                if (!$cassette->isDot()) {
                    if ('.cassette' == substr($cassette->getFilename(), -9, 9)) {
                        unlink($cassette->getPathname());
                    }
                }
            }
        }

        return $this;
    }

    private function cleanCodePHPCSFixer_FixFile($pathToPSR2, $phpCsFixerRules)
    {
        ob_start();
        $command = "vendor/bin/php-cs-fixer fix -q --allow-risky=yes --cache-file=/tmp/php_cs_fixer.cache --rules='".json_encode($phpCsFixerRules)."' {$pathToPSR2}";
        echo " > {$pathToPSR2} ... ";
        $begin = microtime(true);
        //echo $command."\n\n";
        system($command, $junk);
        //exit;
        $time = microtime(true) - $begin;
        ob_end_clean();
        echo ' ['.ConsoleHelper::COLOR_GREEN.'Complete'.ConsoleHelper::COLOR_RESET.' in '.number_format($time, 2)."]\n";

        return $this;
    }

    private function getRoutes($remoteApiUri = false)
    {
        if ($remoteApiUri) {
            $client = new Client([
                'base_uri' => $remoteApiUri,
                'timeout' => 30.0,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            $result = $client->get('/v1')->getBody()->getContents();
            $body = json_decode($result, true);

            return $body['Routes'];
        }
        $response = $this->makeRequest('GET', '/v1');
        $body = (string) $response->getBody();
        $body = json_decode($body, true);

        return $body['Routes'];
    }

    /**
     * @param array $post
     * @param bool  $isJsonRequest
     *
     * @return Response
     */
    private function makeRequest(string $method, string $path, $post = null, $isJsonRequest = true)
    {
        /**
         * @var \Slim\App
         * @var \Gone\AppCore\App $applicationInstance
         */
        $applicationInstance = App::Instance();
        $calledClass = get_called_class();

        if (defined("{$calledClass}")) {
            $modelName = $calledClass::MODEL_NAME;
            if (file_exists("src/Routes/{$modelName}Route.php")) {
                require "src/Routes/{$modelName}Route.php";
            }
        } else {
            if (file_exists('src/Routes.php')) {
                require 'src/Routes.php';
            }
        }
        if (file_exists('src/RoutesExtra.php')) {
            require 'src/RoutesExtra.php';
        }
        if (file_exists('src/Routes') && is_dir('src/Routes')) {
            $count = $applicationInstance->addRoutePathsRecursively('src/Routes');
            //echo "Added {$count} route files\n";
        }

        $applicationInstance->loadAllRoutes();
        $app = $applicationInstance->getApp();

        //$app = Router::Instance()->populateRoutes($app);

        $envArray = array_merge($this->defaultEnvironment, $this->defaultHeaders);
        $envArray = array_merge($envArray, [
            'REQUEST_URI' => $path,
            'REQUEST_METHOD' => $method,
        ]);

        $env = Environment::mock($envArray);
        $uri = Uri::createFromEnvironment($env);
        $headers = Headers::createFromEnvironment($env);

        $cookies = [];
        $serverParams = $env->all();
        $body = new RequestBody();
        if (!is_array($post) && null != $post) {
            $body->write($post);
            $body->rewind();
        } elseif (is_array($post) && count($post) > 0) {
            $body->write(json_encode($post));
            $body->rewind();
        }

        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);
        if ($isJsonRequest) {
            $request = $request->withHeader('Content-type', 'application/json');
        }
        $response = new Response();
        // Invoke app

        $response = $app->process($request, $response);
        //echo "\nRequesting {$method}: {$path} : ".json_encode($post) . "\n";
        //echo "Response: " . (string) $response->getBody()."\n";
        //exit;

        return $response;
    }

    private function runScript($path = null, $script)
    {
        $output = null;
        if ($path) {
            $execLine = "cd {$path} && ".$script;
        } else {
            $execLine = $script;
        }
        echo "Running: \n";
        echo " > {$execLine}\n";
        exec($execLine, $output);
        $output = implode("\n", $output);
        echo $output;

        return $output;
    }
}
