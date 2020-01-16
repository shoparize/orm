<?php

namespace ⌬\Database;

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
use ⌬\Configuration\Configuration;
use ⌬\Configuration\DatabaseConfig as DbConfig;
use ⌬\Database\Components\Model;
use ⌬\Database\Exception\SchemaToAdaptorException;
use ⌬\Database\Twig\Extensions\ArrayUniqueTwigExtension;

class Laminator
{
    /** @var CaseTransformer */
    public $transSnake2Studly;
    /** @var CaseTransformer */
    public $transStudly2Camel;
    /** @var CaseTransformer */
    public $transStudly2Studly;
    /** @var CaseTransformer */
    public $transCamel2Studly;
    /** @var CaseTransformer */
    public $transSnake2Camel;
    /** @var CaseTransformer */
    public $transSnake2Spinal;
    /** @var CaseTransformer */
    public $transCamel2Snake;
    /** @var string Path to code source. */
    private $workPath;
    /** @var Configuration */
    private static $benzineConfig;
    private $config = [
        'templates' => [],
        'formatting' => [],
        'sql' => [],
        'clean' => [],
    ];
    private static $useClassPrefixes = false;
    /** @var \Twig\Loader\FilesystemLoader */
    private $loader;
    /** @var \Twig\Environment */
    private $twig;
    /** @var Adapter[] */
    private $adapters;
    /** @var Metadata[] */
    private $metadatas;
    private $ignoredTables = [];

    private $waitForKeypressEnabled = true;

    private $pathsToPSR2 = [
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
        'vendor/gone.io/appcore',
        'vendor/gone.io/automize',
        'vendor/gone.io/inflection',
        'vendor/gone.io/sessions',
        'vendor/gone.io/testing',
        'vendor/gone.io/twig-extension-inflection',
        'vendor/gone.io/twig-extension-transform',
        'vendor/gone.io/uuid',
        'vendor/gone.io/Laminator',
    ];
    private $phpCsFixerRules = [
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

    private $defaultEnvironment = [];
    private $defaultHeaders = [];

    private $coverageReport;

    public function __construct(string $workPath, Configuration $benzineConfig)
    {
        $this->workPath = $workPath;
        self::$benzineConfig = $benzineConfig;
        set_exception_handler([$this, 'exception_handler']);
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

        $databaseConfigs = self::$benzineConfig->getDatabases();

        // Decide if we're gonna use class prefixes. You don't want to do this if you have a single DB,
        // or you'll get classes called DefaultThing instead of just Thing.
        if (isset($this->config['database'], $this->config['database']['useClassPrefixes']) && true == $this->config['database']['useClassPrefixes']) {
            self::classPrefixesOn();
        } elseif (!is_array($databaseConfigs)) {
            self::classPrefixesOff();
        } elseif (isset($databaseConfigs['Default']) && 1 == count($databaseConfigs)) {
            self::classPrefixesOff();
        } else {
            self::classPrefixesOn();
        }

        if ($databaseConfigs instanceof DbConfig) {
            foreach ($databaseConfigs->__toArray() as $dbName => $databaseConfig) {
                $this->adapters[$dbName] = new \⌬\Database\Adapter($databaseConfig);
                $this->metadatas[$dbName] = new Metadata($this->adapters[$dbName]);
                $this->adapters[$dbName]->query('set global innodb_stats_on_metadata=0;');
            }
        }

        return $this;
    }

    public function getBenzineConfig(): Configuration
    {
        return self::BenzineConfig();
    }

    public static function BenzineConfig(): Configuration
    {
        return self::$benzineConfig;
    }

    public function getWorkPath(): string
    {
        return $this->workPath;
    }

    public function exception_handler($exception)
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

    public static function classPrefixesOn()
    {
        echo "Class prefixes ON\n";
        self::$useClassPrefixes = true;
    }

    public static function classPrefixesOff()
    {
        echo "Class prefixes OFF\n";
        self::$useClassPrefixes = false;
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
        $sql = "SHOW columns FROM `{$table}` WHERE extra LIKE '%auto_increment%'";
        $query = $adapter->query($sql);
        $columns = [];

        foreach ($query->execute() as $aiColumn) {
            $columns[] = $aiColumn['Field'];
        }

        return $columns;
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
        $this->removeCoreGeneratedFiles();
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

    public function sendSDKToGit($path)
    {
        if (isset($this->config['sdk']['output']['git']['repo'])) {
            echo "Sending SDK to Git:\n";

            if ($this->coverageReport) {
                $coverageStatement = sprintf(
                    '%s coverage',
                    $this->coverageReport->project[0]->directory[0]->totals->lines->attributes()->percent
                );
            } else {
                $coverageStatement = 'No coverage available.';
            }
            if (isset($this->config['sdk']['output']['git']['author']['name'], $this->config['sdk']['output']['git']['author']['email'])) {
                $this->runScript($path, "git config --global user.email \"{$this->config['sdk']['output']['git']['author']['email']}\"");
                $this->runScript($path, "git config --global user.name \"{$this->config['sdk']['output']['git']['author']['name']}\"");
            }
            $this->runScript($path, 'git commit -m "Updated PHPVCR Cassettes." tests/fixtures');
            $this->runScript($path, 'git add tests/');
            $this->runScript($path, "git commit -m \"Updated Tests. {$coverageStatement}\" tests");
            $this->runScript($path, 'git add src/');
            $this->runScript($path, 'git add .gitignore');
            $this->runScript($path, 'git add bootstrap.php composer.* Dockerfile phpunit.xml.dist Readme.md run-tests.sh test-compose.yml');
            $this->runScript($path, "git commit -m \"Updated Library. {$coverageStatement}\"");
            $this->runScript($path, 'git push origin master');
        } else {
            echo "Skipping GIT step, not configured in Laminator.yml: (sdk->output->git->repo)\n";
        }

        return $this;
    }

    public function runSdkifier($sdkOutputPath = false, $remoteApiUri = false)
    {
        if (!$sdkOutputPath) {
            $sdkOutputPath = 'vendor/gone.io/lib'.strtolower(APP_NAME).'/';
            if (isset($this->config['sdk'], $this->config['sdk']['output'], $this->config['sdk']['output']['path'])) {
                $sdkOutputPath = ''.$this->config['sdk']['output']['path'];
            }
        }

        $this
            //->purgeSDK($sdkOutputPath)
            //->checkGitSDK($sdkOutputPath)
            ->makeSDK($sdkOutputPath, $remoteApiUri, false)
            ->cleanCodePHPCSFixer([$sdkOutputPath])
            //->runSDKTests($sdkOutputPath)
            //->sendSDKToGit($sdkOutputPath)
        ;
    }

    public function disableWaitForKeypress()
    {
        $this->waitForKeypressEnabled = false;

        return $this;
    }

    public function enableWaitForKeypress()
    {
        $this->waitForKeypressEnabled = true;

        return $this;
    }

    /**
     * @return Model[]
     */
    private function makeModelSchemas(): array
    {
        /** @var Model[] $models */
        $models = [];
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
                        ->setNamespace(self::$benzineConfig->getNamespace())
                        ->setAdaptor($adapter)
                        ->setDatabase($dbName)
                        ->setTable($table->getName())
                        ->computeColumns($table->getColumns())
                        ->computeConstraints($table->getConstraints())
                    ;
                    $models[$oModel->getClassName()] = $oModel;
                }
                ksort($models);
            }
        }

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

    /**
     * @param $outputPath
     * @param bool $remoteApiUri
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return $this
     */
    private function makeSDKFiles($outputPath = APP_ROOT, $remoteApiUri = false)
    {
        $packs = [];
        $routeCount = 0;
        $sharedRenderData = [
            'app_namespace' => APP_NAMESPACE,
            'app_name' => APP_NAME,
            'app_container' => APP_CORE_NAME,
            'default_base_url' => strtolower('http://'.APP_NAME.'.local'),
            'release_time' => date('Y-m-d H:i:s'),
        ];

        $routes = $this->getRoutes($remoteApiUri);
        echo 'Found '.count($routes)." routes.\n";
        if (count($routes) > 0) {
            foreach ($routes as $route) {
                if (isset($route['name'])) {
                    if (isset($route['class'])) {
                        $packs[(string) $route['class']][(string) $route['function']] = $route;
                        ++$routeCount;
                    }
                }
            }
        } else {
            die("Cannot find any routes while building SDK. Something has gone very wrong.\n\n");
        }

        echo "Generating SDK for {$routeCount} routes...\n";
        // "SDK" suite
        foreach ($packs as $packName => $routes) {
            echo " > Pack: {$packName}...\n";
            $scopeName = $packName;
            $scopeName[0] = strtolower($scopeName[0]);
            $routeRenderData = [
                'pack_name' => $packName,
                'scope_name' => $scopeName,
                'routes' => $routes,
            ];
            $properties = [];
            $propertiesOptions = [];
            foreach ($routes as $route) {
                if (isset($route['properties'])) {
                    foreach ($route['properties'] as $property) {
                        $properties[] = $property;
                    }
                }
                if (isset($route['propertiesOptions'])) {
                    foreach ($route['propertiesOptions'] as $propertyName => $propertyOption) {
                        $propertiesOptions[$propertyName] = $propertyOption;
                    }
                }
            }

            $properties = array_unique($properties);
            $routeRenderData['properties'] = $properties;
            $routeRenderData['propertiesOptions'] = $propertiesOptions;
            $routeRenderData = array_merge($sharedRenderData, $routeRenderData);
            //\Kint::dump($routeRenderData);

            // Access Layer
            $this->renderToFile(true, $outputPath."/src/AccessLayer/Base/Base{$packName}AccessLayer.php", 'SDK/AccessLayer/baseaccesslayer.php.twig', $routeRenderData);
            $this->renderToFile(false, $outputPath."/src/AccessLayer/{$packName}AccessLayer.php", 'SDK/AccessLayer/accesslayer.php.twig', $routeRenderData);

            // Models
            $this->renderToFile(true, $outputPath."/src/Models/Base/Base{$packName}Model.php", 'SDK/Models/basemodel.php.twig', $routeRenderData);
            $this->renderToFile(false, $outputPath."/src/Models/{$packName}Model.php", 'SDK/Models/model.php.twig', $routeRenderData);

            // Tests
            $this->renderToFile(true, $outputPath."/tests/AccessLayer/{$packName}Test.php", 'SDK/Tests/AccessLayer/client.php.twig', $routeRenderData);

            if (!file_exists($outputPath.'/tests/fixtures')) {
                mkdir($outputPath.'/tests/fixtures', 0777, true);
            }
        }

        $renderData = array_merge(
            $sharedRenderData,
            [
                'packs' => $packs,
                'config' => $this->config,
            ]
        );

        echo 'Generating Client Container:';
        $this->renderToFile(true, $outputPath.'/src/Client.php', 'SDK/client.php.twig', $renderData);
        echo ' ['.ConsoleHelper::COLOR_GREEN.'DONE'.ConsoleHelper::COLOR_RESET."]\n";

        echo 'Generating Composer.json:';
        $this->renderToFile(true, $outputPath.'/composer.json', 'SDK/composer.json.twig', $renderData);
        echo ' ['.ConsoleHelper::COLOR_GREEN.'DONE'.ConsoleHelper::COLOR_RESET."]\n";

        echo 'Generating Test Bootstrap:';
        $this->renderToFile(true, $outputPath.'/bootstrap.php', 'SDK/bootstrap.php.twig', $renderData);
        echo ' ['.ConsoleHelper::COLOR_GREEN.'DONE'.ConsoleHelper::COLOR_RESET."]\n";

        echo 'Generating phpunit.xml, documentation, etc:';
        $this->renderToFile(true, $outputPath.'/phpunit.xml.dist', 'SDK/phpunit.xml.twig', $renderData);
        $this->renderToFile(true, $outputPath.'/Readme.md', 'SDK/readme.md.twig', $renderData);
        $this->renderToFile(true, $outputPath.'/.gitignore', 'SDK/gitignore.twig', $renderData);
        $this->renderToFile(true, $outputPath.'/Dockerfile.tests', 'SDK/Dockerfile.twig', $renderData);
        $this->renderToFile(true, $outputPath.'/test-compose.yml', 'SDK/docker-compose.yml.twig', $renderData);
        echo ' ['.ConsoleHelper::COLOR_GREEN.'DONE'.ConsoleHelper::COLOR_RESET."]\n";

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
