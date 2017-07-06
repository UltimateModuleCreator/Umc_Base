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
namespace Umc\Base\Api\Data;

/**
 * @api
 */
interface ModuleInterface extends ModelInterface
{
    const ENTITY_CODE = 'umc_module';

    /**
     * Field constants
     */
    const NAMESPACE_FIELD       = 'namespace';
    const MODULE_NAME           = 'module_name';
    const LICENSE               = 'license';
    const ANNOTATION            = 'annotation';
    const UNDERSCORE            = 'underscore';
    const MENU_PARENT           = 'menu_parent';
    const QUALIFIED             = 'qualified';
    const VERSION               = 'version';
    const UI_VERSION            = 'ui_version';
    const MENU_TEXT             = 'menu_text';
    const SORT_ORDER            = 'sort_order';
    const CREATE_COMPOSER       = 'create_composer';
    const COMPOSER_NAME         = 'composer_name';
    const COMPOSER_DESCRIPTION  = 'composer_description';
    const COMPOSER_LICENSE      = 'composer_license';
    const CREATE_LICENSE        = 'create_license';
    const CREATE_README         = 'create_readme';
    const README                = 'readme';
    const COMPOSER_VERSION      = 'composer_version';

    /**
     * @param bool $toLower
     * @return string
     */
    public function getNamespace($toLower = false);

    /**
     * @param string $namespace
     * @return ModuleInterface
     */
    public function setNamespace($namespace);

    /**
     * @param bool $toLower
     * @return mixed
     */
    public function getModuleName($toLower = false);

    /**
     * @param string $moduleName
     * @return ModuleInterface
     */
    public function setModuleName($moduleName);

    /**
     * @return string
     */
    public function getExtensionName();

    /**
     * @return int[]
     */
    public function getNameAttributes();

    /**
     * @return EntityInterface[]
     */
    public function getEntities();

    /**
     * init module from data
     *
     * @param array $data
     * @return $this
     */
    public function initFromData(array $data);

    /**
     * @return string
     */
    public function getLicense();

    /**
     * @param string $license
     * @return ModuleInterface
     */
    public function setLicense($license);

    /**
     * @return bool
     */
    public function getAnnotation();

    /**
     * @param bool $annotation
     * @return ModuleInterface
     */
    public function setAnnotation($annotation);

    /**
     * @return string
     */
    public function getUnderscoreValue();

    /**
     * @return bool
     */
    public function getQualified();

    /**
     * @param bool $qualified
     * @return ModelInterface
     */
    public function setQualified($qualified);

    /**
     * @param string $menuParent
     * @return ModuleInterface
     */
    public function setMenuParent($menuParent);

    /**
     * @return string
     */
    public function getMenuParent();

    /**
     * @return string
     */
    public function getVersion();

    /**
     * @param string $version
     * @return ModuleInterface
     */
    public function setVersion($version);

    /**
     * @return string
     */
    public function getMenuText();

    /**
     * @param string $menuText
     * @return ModuleInterface
     */
    public function setMenuText($menuText);

    /**
     * @return string
     */
    public function getSortOrder();

    /**
     * @param string $sortOrder
     * @return ModuleInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * @return string
     */
    public function getCreateComposer();

    /**
     * @param string $createComposer
     * @return ModuleInterface
     */
    public function setCreateComposer($createComposer);

    /**
     * @return string
     */
    public function getComposerName();

    /**
     * @param string $composerName
     * @return ModuleInterface
     */
    public function setComposerName($composerName);

    /**
     * @return string
     */
    public function getComposerDescription();

    /**
     * @param string $composerDescription
     * @return ModuleInterface
     */
    public function setComposerDescription($composerDescription);

    /**
     * @return string
     */
    public function getComposerLicense();

    /**
     * @param string $composerLicense
     * @return ModuleInterface
     */
    public function setComposerLicense($composerLicense);

    /**
     * @return string
     */
    public function getCreateLicense();

    /**
     * @param string $createLicense
     * @return ModuleInterface
     */
    public function setCreateLicense($createLicense);

    /**
     * @return string
     */
    public function getCreateReadme();

    /**
     * @param string $createReadme
     * @return ModuleInterface
     */
    public function setCreateReadme($createReadme);

    /**
     * @return string
     */
    public function getReadme();

    /**
     * @param string $readme
     * @return ModuleInterface
     */
    public function setReadme($readme);

    /**
     * @return string
     */
    public function getComposerVersion();

    /**
     * @param string $composerVersion
     * @return ModuleInterface
     */
    public function setComposerVersion($composerVersion);

    /**
     * @return int
     */
    public function getUiVersion();

    /**
     * @param string $uiVersion
     * @return ModuleInterface
     */
    public function setUiVersion($uiVersion);

    /**
     * @param EntityInterface $entity
     * @return ModuleInterface
     */
    public function addEntity(EntityInterface $entity);

    /**
     * @return RelationInterface[]
     */
    public function getRelations();

    /**
     * @return string
     */
    public function getRelationsAsJson();
}
