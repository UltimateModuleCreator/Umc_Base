<?php
/**
 * Umc_Base extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Umc
 * @package   Umc_Base
 * @copyright Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Generator;

use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Module\Dir\Reader as ModuleReader;
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\ClassConfig;
use Umc\Base\Generator\Validator\Dependency;
use Umc\Base\Processor\ProcessorInterface;
use Umc\Base\Provider\ProviderInterface;

class ClassGenerator extends AbstractGenerator
{
    /**
     * constructor rows separator
     *
     * @var string
     */
    const CONSTRUCT_SEPARATOR = '###';

    /**
     * class pattern regex
     *
     * @var string
     */
    const CLASS_PATTERN = '/{{class\s*(.*?)}}/si';

    /**
     * generated class namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * generated class name
     *
     * @var string
     */
    protected $className;

    /**
     * generated class parent
     *
     * @var array
     */
    protected $extends;

    /**
     * interfaces implemented by current class
     *
     * @var array
     */
    protected $implements = [];

    /**
     * additional constructor instructions
     *
     * @var array
     */
    protected $constructs = [];

    /**
     * current class member vars
     *
     * @var array
     */
    protected $members = [];

    /**
     * current class constructor
     *
     * @var string
     */
    protected $constructor;

    /**
     * class mapping config
     *
     * @var \Umc\Base\Config\ClassConfig
     */
    protected $classConfig;

    /**
     * class annotations
     * @var array
     */
    protected $annotations;

    /**
     * use statements for the generated class
     *
     * @var array
     */
    protected $uses = [];

    /**
     * base types
     *
     * @var array
     */
    protected $baseTypes;

    /**
     * license text
     *
     * @var string
     */
    protected $license;

    /**
     * @var string
     */
    protected $parentConstructExtraParams = '';

    /**
     * @var array
     */
    protected $skipConstructorTypes;

    /**
     * ClassGenerator constructor.
     * @param ModuleReader $moduleReader
     * @param IoFile $io
     * @param ProviderInterface $modelProvider
     * @param Dependency $dependencyValidator
     * @param string $defaultScope
     * @param ClassConfig $classConfig
     * @param BaseTypes $types
     * @param array $skipConstructorTypes
     * @param array $processors
     * @param array $data
     */
    public function __construct(
        ModuleReader $moduleReader,
        IoFile $io,
        ProviderInterface $modelProvider,
        Dependency $dependencyValidator,
        $defaultScope,
        ClassConfig $classConfig,
        BaseTypes $types,
        array $skipConstructorTypes = [],
        array $processors = [],
        array $data = []
    ) {
        $this->classConfig          = $classConfig;
        $this->baseTypes            = $types->getTypes();
        $this->skipConstructorTypes = $skipConstructorTypes;
        parent::__construct(
            $moduleReader,
            $io,
            $modelProvider,
            $dependencyValidator,
            $defaultScope,
            $processors,
            $data
        );
    }

    /**
     * reset values
     *
     * @return $this
     */
    protected function reset()
    {
        $this->namespace                    = '';
        $this->className                    = '';
        $this->implements                   = [];
        $this->constructs                   = [];
        $this->extends                      = '';
        $this->members                      = '';
        $this->constructor                  = '';
        $this->uses                         = [];
        $this->parentConstructExtraParams   = '';
        return $this;
    }

    /**
     * build content for model
     *
     * @param ModelInterface $model
     * @return string
     */
    public function buildContent(ModelInterface $model)
    {
        $this->reset();
        $this->generateInterfaces($model);
        $content = $this->parseParts($model);
        $this->generateClassNameAndNamespace($model);
        $this->generateExtends($model);
        $this->generateMembers($model);
        $this->generateAnnotations($model);
        $this->generateConstructorInstructions($model);
        $this->generateParentConstructExtraParams($model);
        $content = $this->mergeElements($model, $content);
        return $content;
    }

    /**
     * generate class name and namespace from file name
     *
     * @param ModelInterface $model
     * @return $this
     */
    protected function generateClassNameAndNamespace(ModelInterface $model)
    {
        $destination = $model->filterContent($this->config['destination']);
        $this->className = '';
        $this->namespace = '';
        $namespace = $this->module->getNamespace().
            '\\'.$this->module->getModuleName().
            '\\'.str_replace(['.php', '/'], ['', '\\'], $destination);
        $parts = explode('\\', $namespace);
        $class = $parts[(count($parts) - 1)];
        unset($parts[(count($parts) - 1)]);
        $namespace = implode('\\', $parts);
        $namespace = 'namespace '.$namespace.';'.$this->getEol().$this->getEol();
        $this->className = $this->getScopeModel()->filterContent($class);
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * generate the parent class name
     *
     * @param ModelInterface $model
     * @return $this
     */
    protected function generateExtends(ModelInterface $model)
    {
        $this->extends = '';
        if (isset($this->config['extends'])) {
            $classData = $this->classConfig->getClassData($this->config['extends']);
            $extends = [
                'class' => $model->filterContent($classData['class']),
                'alias' => $model->filterContent($classData['alias'])
            ];
            $this->extends = $extends;
        }
        return $this;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function postProcess($content)
    {
        $fullContent = $this->getHeader().
            $this->getLicense().
            $this->namespace.
            rtrim($content).
            $this->getEol().
            $this->getFooter();
        return $fullContent;
    }

    /**
     * get the file header
     *
     * @return string
     */
    protected function getHeader()
    {
        return '<?php'.$this->getEol();
    }

    /**
     * get the file footer
     *
     * @return string
     */
    protected function getFooter()
    {
        return '}'.$this->getEol();
    }

    /**
     * get license text
     *
     * @return string
     */
    protected function getLicense()
    {
        if ($this->license === null) {
            $text = $this->module->getLicense();
            $eol = $this->getEol();
            $license = trim($text);
            if (!$license) {
                $this->license = '';
                return $this->license;
            }
            while (strpos($license, '*/') !== false) {
                $license = str_replace('*/', '', $license);
            }
            while (strpos($license, '/*') !== false) {
                $license = str_replace('/*', '', $license);
            }
            while (strpos($license, '<!--') !== false) {
                $license = str_replace('<!--', '', $license);
            }
            while (strpos($license, '-->') !== false) {
                $license = str_replace('-->', '', $license);
            }
            $lines = explode("\n", $license);
            $top = '';
            $processed = $top.'/**'.$eol;
            foreach ($lines as $line) {
                $processed .= ' * '.$line.$eol;
            }
            $processed .= ' */'.$eol;
            $this->license = $this->module->filterContent($processed);
        }
        return $this->license;
    }

    /**
     * @param ModelInterface $model
     * @return $this
     */
    protected function generateInterfaces(ModelInterface $model)
    {
        $config = $this->config;
        $this->implements = [];
        if (isset($config['implements']['implement'])) {
            foreach ($config['implements']['implement'] as $implement) {
                if ($this->dependencyValidator->validateDepend($model, $implement)) {
                    $scope = (isset($implement['scope']) ? $implement['scope'] : $this->getDefaultScope());
                    $processor = $this->getImplementProcessor($scope);
                    $this->implements = array_merge($this->implements, $processor->process($model, $implement));
                }
            }
        }
        return $this;
    }

    /**
     * generate annotations
     *
     * @param ModelInterface $model
     * @return $this
     */
    protected function generateAnnotations(ModelInterface $model)
    {
        $this->annotations = [];
        $config = $this->config;
        if ($this->module->getAnnotation() && isset($config['annotations']['annotation'])) {
            foreach ($config['annotations']['annotation'] as $annotation) {
                $scope = (isset($annotation['scope']) ? $annotation['scope'] : $this->getDefaultScope());
                $processor = $this->getAnnotationProcessor($scope);
                $annotation = $this->filterClasses($model, $annotation, false);
                $annotations = $processor->process($model, $annotation);
                if ($annotations) {
                    $this->annotations = array_merge($this->annotations, $annotations);
                }
            }

        }
        return $this;
    }

    /**
     * @param ModelInterface $model
     * @return $this
     */
    protected function generateMembers(ModelInterface $model)
    {
        $this->members = [];
        $this->constructor = '';
        $config = $this->config;
        if (isset($config['members']['member'])) {
            foreach ($config['members']['member'] as $member) {
                $scope = (isset($member['scope']) ? $member['scope'] : $this->getDefaultScope());
                $processor = $this->getMemberProcessor($scope);
                $members = $processor->process($model, $member);
                if ($members) {
                    $this->members = array_merge($this->members, $members);
                }
            }
        }
        return $this;
    }

    /**
     * format class members
     *
     * @param array $member
     * @return string
     */
    protected function formatMember($member)
    {
        $name       = $member['id'];
        $var        = $member['var'];
        $doc        = $member['doc'];
        $access     = $member['type'];
        $default    = $member['default'];
        $const      = $member['constant'];
        $eol        = $this->getEol();
        $tab        = $this->getPadding();
        if ($const) {
            $prefix = '';
            $underscore = '';
        } else {
            $prefix = '$';
            $underscore = (!$member['core']) ? $this->getUnderscore() : '';
        }

        $className = $this->getClassName($var);
        if (!$this->getQualified()) {
            $this->uses[$var['class']] = $var;
        }

        return $tab.'/**'.$eol.
            $tab.' * '. $doc . $eol.
            $tab.' * '. $eol.
            $tab.' * @var '.$className.$eol.
            $tab.' */'.$eol.
            $tab.$access.' '.$prefix.$underscore.$name.
            (($default != '') ? ' = '.$default : '').';'.$eol.$eol;
    }

    /**
     * get underscore value for protected members
     *
     * @return string
     */
    public function getUnderscore()
    {
        return $this->module->getUnderscoreValue();
    }

    /**
     * get underscore value for specific member
     *
     * @param array $settings
     * @return string
     */
    protected function getUnderscoreValue($settings)
    {
        if ($this->classConfig->getBoolValue($settings, 'core')) {
            return '';
        }
        return $this->getUnderscore();
    }

    /**
     * check if fully qualified names are used
     *
     * @return bool
     */
    public function getQualified()
    {
        return $this->module->getQualified();
    }

    /**
     * get processor for member variables
     *
     * @param string $type
     * @return ProcessorInterface
     * @throws \Exception
     */
    protected function getMemberProcessor($type)
    {
        return $this->getProcessor(self::MEMBER_PROCESSOR_KEY, $type);
    }

    /**
     * get processor for annotations
     *
     * @param string $type
     * @return ProcessorInterface
     * @throws \Exception
     */
    protected function getAnnotationProcessor($type)
    {
        return $this->getProcessor(self::ANNOTATION_PROCESSOR_KEY, $type);
    }

    /**
     * @param string $type
     * @return ProcessorInterface
     */
    protected function getImplementProcessor($type)
    {
        return $this->getProcessor(self::IMPLEMENT_PROCESSOR_KEY, $type);
    }

    /**
     * @param string $type
     * @return ProcessorInterface
     */
    protected function getConstructProcessor($type)
    {
        return $this->getProcessor(self::CONSTRUCT_PROCESSOR_KEY, $type);
    }

    /**
     * @return string
     */
    protected function aggregateAnnotations()
    {
        if ($this->module->getAnnotation() && count($this->annotations)) {
            return '/**'.$this->getEol().implode($this->getEol(), $this->annotations).
                $this->getEol().' */'.$this->getEol();
        }
        return '';
    }

    /**
     * @return string
     */
    protected function aggregateClassLine()
    {
        if ($this->config['interface']) {
            $classLine = 'interface ';
        } elseif ($this->config['abstract']) {
            $classLine = 'abstract class ';
        } else {
            $classLine = 'class ';
        }
        $classLine .= $this->className;
        if ($this->extends) {
            $classLine .= ' extends '.$this->getClassName($this->extends);
            if (!$this->getQualified()) {
                $this->uses[$this->extends['class']] = $this->extends;
            }
        }
        if ($this->implements) {
            $interfaces = [];
            foreach ($this->implements as $classData) {
                $interfaces[] = $this->getClassName($classData);
                if (!$this->getQualified()) {
                    $this->uses[$classData['class']] = $classData;
                }
            }
            $classLine .= ' implements '. implode(', ', $interfaces);
        }
        $classLine .= $this->getEol().'{'.$this->getEol();
        return $classLine;
    }

    /**
     * @param array $forConstruct
     * @return array
     */
    protected function aggregateConstructorDocBlock(array $forConstruct)
    {
        $lines[] = '/**';
        $lines[] = ' * constructor';
        $lines[] = ' * ';
        foreach ($forConstruct as $param) {
            $className = $this->getClassName($param['var']);
            if (!$this->getQualified()) {
                $this->uses[$param['var']['class']] = $param['var'];
            }
            $lines[] = ' * @param '.
                $className.
                (($className) ? ' ' : '').
                '$'.
                $param['id'];
        }
        $lines[] = ' */';
        return $lines;
    }

    /**
     * @param array $forConstruct
     * @return array
     */
    protected function aggregateConstructorSignature(array $forConstruct)
    {
        $tab = $this->getPadding();
        $lines = [];
        $lines[] = 'public function __construct(';
        $forConstructCount = count($forConstruct);
        foreach ($forConstruct as $index => $param) {
            $className = $this->getClassName($param['var']);
            if (!$this->getQualified()) {
                $this->uses[$param['var']['class']] = $param['var'];
            }
            if (in_array($className, $this->skipConstructorTypes)) {
                $className = '';
            }
            $lines[] = $tab.$className.
                (($className) ? ' ' : '').
                '$'.$param['id'].
                ($param['default'] ? ' = '.$param['default'] : '').
                (($index < $forConstructCount - 1) ? ',' : '');
        }
        $lines[] = ') {';
        return $lines;
    }

    /**
     * @param array $forConstruct
     * @return string
     */
    protected function aggregateConstructor(array $forConstruct)
    {
        $eol = $this->getEol();
        $tab = $this->getPadding();
        $lines = $this->aggregateConstructorDocBlock($forConstruct);
        $lines = array_merge($lines, $this->aggregateConstructorSignature($forConstruct));
        //constructor body
        $constructAssign = [];
        foreach ($forConstruct as $param) {
            if (!$param['parent'] && !$param['skip']) {
                $underscore = $this->getUnderscoreValue($param);
                $constructAssign[] = $tab.'$this->'.$underscore.$param['id'].' = '.'$'.$param['id'].';';
            }
        }
        $constructAssign = $this->beautifyConstructAssigns($constructAssign);
        foreach ($constructAssign as $line) {
            $lines[] = $line;
        }
        if ($this->extends) {
            $parentLine = $tab.'parent::__construct(';
            $parentParams = [];
            foreach ($forConstruct as $param) {
                if ($param['parent']) {
                    $parentParams[] = '$'.$param['id'];
                }
            }
            if ($this->parentConstructExtraParams) {
                $parentParams[] = $this->parentConstructExtraParams;
            }
            $parentLine .= implode(', ', $parentParams).');';
            $lines[] = $parentLine;
        }
        foreach ($this->constructs as $construct) {
            $parts = explode(self::CONSTRUCT_SEPARATOR, $construct);
            foreach ($parts as $part) {
                $lines[] = $tab.$part;
            }
        }
        $lines[] = '}'.$eol;
        return $this->getScopeModel()->filterContent($tab.implode($eol.$tab, $lines).$eol);
    }

    /**
     * @param ModelInterface $model
     * @param string $content
     * @param bool $greedy
     * @return mixed
     */
    protected function filterClasses(ModelInterface $model, $content, $greedy = true)
    {
        $pattern = self::CLASS_PATTERN;
        if (!$greedy) {
            $pattern .= 'U';
        }
        if (is_array($content)) {
            foreach ($content as $key => $value) {
                $content[$key] = $this->filterClasses($model, $value, $greedy);
            }
        } else {
            if (preg_match_all($pattern, $content, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $construction) {
                    if (isset($construction[1])) {

                        $classData = $this->classConfig->getClassData(trim($construction[1]));
                        $classAndUse = $this->getUsesAndClassName($classData);
                        if ($classAndUse['use']) {
                            $use = $classAndUse['use'];
                            if (!isset($this->uses[$use['class']])) {
                                $this->uses[$use['class']] = $use;
                            }
                        }
                        $replaceVal = $classAndUse['class'];
                        $content = str_replace($construction[0], $replaceVal, $content);
                    }
                }
            }
        }
        return $content;
    }

    /**
     * @return string
     */
    protected function aggregateUses()
    {
        $uses = '';
        $useLines = [];
        if (!$this->getQualified()) {
            foreach ($this->uses as $use) {
                if (!in_array($use['class'], $this->baseTypes)) {
                    $useLines[] = 'use '.ltrim($use['class'], '\\'). (($use['alias']) ? ' as '.$use['alias'] : '').';';
                }
            }
            if (count($useLines)) {
                asort($useLines);
                $useLines = array_unique($useLines);
                $uses = implode($this->getEol(), $useLines).$this->getEol().$this->getEol();
            }
        }
        return $uses;
    }

    /**
     * merge all elements of the class
     *
     * @param ModelInterface $model
     * @param string $string
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function mergeElements(ModelInterface $model, $string)
    {
        $content = '';
        if ($this->config['api']) {
            $content .= '/**'.$this->getEol().' * @api'.$this->getEol().' */'.$this->getEol();
        }
        //add annotations
        $content .= $this->aggregateAnnotations();
        $content .= $this->aggregateClassLine();
        //separate members
        $constants = [];
        $members = [];
        $forConstruct = [];
        $hasConstruct = false;
        foreach ($this->members as $member) {
            if ($member['constant']) {
                $constants[] = $member;
                continue;
            }
            if ($member['show']) {
                $members[] = $member;
            }
            if ($member['construct']) {
                $forConstruct[] = $member;
                if (!$member['parent']) {
                    $hasConstruct = true;
                }
            }
        }
        //add constants
        foreach ($constants as $constant) {
            $content .= $this->formatMember($constant);
        }
        //add members
        foreach ($members as $member) {
            $content .= $this->formatMember($member);
        }
        if (count($this->constructs)) {
            $hasConstruct = true;
        }
        //generate constructor
        if ($hasConstruct) {
            $content .= $this->aggregateConstructor($forConstruct);
        }
        //add class body
        $content .= $string;
        //filter {{class}} directives
        $content = $this->filterClasses($model, $content);
        //process uses
        $uses = $this->aggregateUses();

        return $uses.$content;
    }

    /**
     * beautify the constructor assignments
     *
     * $this->something = $something
     * $this->somethingElse = $something else
     *
     * becomes
     *
     * $this->something     = $something
     * $this->somethingElse = $something else
     *
     * @param array $vars
     * @return array
     */
    protected function beautifyConstructAssigns($vars)
    {
        $max = 0;
        //determine the longest left side
        foreach ($vars as $var) {
            $parts = explode('=', $var);
            if (strlen(rtrim($parts[0])) > $max) {
                $max = strlen(rtrim($parts[0]));
            }
        }
        //make all use the max length + 1;
        $lines = [];
        foreach ($vars as $var) {
            $parts = explode('=', $var);
            $parts[0] = rtrim($parts[0]).str_repeat(' ', $max + 1 - strlen(rtrim($parts[0])));
            $lines[] = implode('=', $parts);
        }
        return $lines;
    }

    /**
     * get use statements and class name
     *
     * @param array $classData
     * @return array
     */
    protected function getUsesAndClassName($classData)
    {
        if ($this->getQualified()) {
            return [
                'class' => '\\'.$classData['class'],
                'use' => null
            ];
        } else {
            $use = $classData;
            if (!$classData['alias']) {
                $parts = explode('\\', $classData['class']);
                $class = $parts[count($parts) - 1];
            } else {
                $class = $classData['alias'];
            }
            return [
                'class' => $class,
                'use'   => $use
            ];
        }
    }

    /**
     * get alias from class array
     *
     * @param array $data
     * @return string
     */
    protected function getAlias($data)
    {
        if (isset($data['alias']) && $data['alias']) {
            return $data['alias'];
        }
        $parts = explode('\\', $data['class']);
        return $parts[count($parts) - 1];
    }

    /**
     * get class name to use in generated file
     *
     * @param array $param
     * @return mixed|string
     */
    protected function getClassName($param)
    {
        if ($this->getQualified()) {
            if (in_array($param['class'], $this->baseTypes)) {
                $className = $param['class'];
            } else {
                $className = '\\'.$param['class'];
            }
        } else {
            $className = $this->getAlias($param);
        }
        return $className;
    }

    /**
     * @param ModelInterface $model
     * @return $this
     */
    protected function generateParentConstructExtraParams(ModelInterface $model)
    {
        $this->parentConstructExtraParams = '';
        if (isset($this->config['parent_construct_extra'])) {
            $this->parentConstructExtraParams = $model->filterContent($this->config['parent_construct_extra']);
        }
        return $this;
    }

    /**
     * @param ModelInterface $model
     * @return array
     */
    protected function generateConstructorInstructions(ModelInterface $model)
    {
        $config = $this->config;
        $this->constructs = [];
        if (isset($config['constructs']['construct'])) {
            foreach ($config['constructs']['construct'] as $construct) {
                $scope = (isset($construct['scope']) ? $construct['scope'] : $this->getDefaultScope());
                $processor = $this->getConstructProcessor($scope);
                $this->constructs = array_merge($this->constructs, $processor->process($model, $construct));
            }
        }
        return $this->constructs;
    }
}
