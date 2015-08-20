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
 * @copyright 2015 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Model\Parser;

use Umc\Base\Model\Config\ClassConfig;
use Umc\Base\Model\Core\AbstractModel;

class Member
{
    /**
     * class config reference
     *
     * @var ClassConfig
     */
    protected $classConfig;

    /**
     * current model
     *
     * @var AbstractModel
     */
    protected $model;

    /**
     * constructor
     *
     * @param ClassConfig $classConfig
     */
    public function __construct(
        ClassConfig $classConfig
    )
    {
        $this->classConfig = $classConfig;
    }

    /**
     * @param AbstractModel $model
     * @return $this
     */
    public function setModel(AbstractModel $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * parse member
     *
     * @param array $member
     * @return array
     */
    public function parse(array $member)
    {
        $data = [];
        $constant = $this->classConfig->getBoolValue($member, 'constant');
        if ($constant) {
            $type = 'const';
        } else {
            $type = (isset($member['type']) ? $member['type'] : 'protected');
        }
        $data['id']         = $this->filterContent($member['id']);
        $data['type']       = $type;
        $data['construct']  = $this->classConfig->getBoolValue($member, 'construct');
        $data['constant']   = $constant;
        $data['parent']     = $this->classConfig->getBoolValue($member, 'parent');
        $data['show']       = $this->classConfig->getBoolValue($member, 'show');
        $data['skip']       = $this->classConfig->getBoolValue($member, 'skip');
        $data['doc']        = (isset($member['doc']) ? $this->filterContent($member['doc']) : '') ;
        if (isset($member['var'])) {
            $classData = $this->classConfig->getClassData($member['var']);
            $data['var'] = [
                'class' => $this->filterContent($classData['class']),
                'alias' => $this->filterContent($classData['alias'])
            ];
        }
        $data['default'] = isset($member['default']) ? $this->filterContent($member['default']) : '';
        $data['core'] = $this->classConfig->getBoolValue($member, 'core');
        return $data;
    }

    /**
     * filter content
     *
     * @param $content
     * @return string
     */
    public function filterContent($content)
    {
        return $this->model->filterContent($content);
    }
}
