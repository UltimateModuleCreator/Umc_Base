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
namespace Umc\Base\Parser;

use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\ClassConfig;
use Umc\Base\Model\AbstractModel;

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
    ) {
        $this->classConfig = $classConfig;
    }

    /**
     * @param ModelInterface $model
     * @param array $member
     * @return array
     */
    public function parse(ModelInterface $model, array $member)
    {
        $data = [];
        $constant = $this->classConfig->getBoolValue($member, 'constant');
        if ($constant) {
            $type = 'const';
        } else {
            $type = (isset($member['type']) ? $member['type'] : 'protected');
        }
        $data['id']         = $model->filterContent($member['id']);
        $data['type']       = $type;
        $data['construct']  = $this->classConfig->getBoolValue($member, 'construct');
        $data['constant']   = $constant;
        $data['parent']     = $this->classConfig->getBoolValue($member, 'parent');
        $data['show']       = $this->classConfig->getBoolValue($member, 'show');
        $data['skip']       = $this->classConfig->getBoolValue($member, 'skip');
        $data['doc']        = (isset($member['doc']) ? $model->filterContent($member['doc']) : '') ;
        if (isset($member['var'])) {
            $classData = $this->classConfig->getClassData($member['var']);
            $data['var'] = [
                'class' => $model->filterContent($classData['class']),
                'alias' => $model->filterContent($classData['alias'])
            ];
        }
        $data['default'] = isset($member['default']) ? $model->filterContent($member['default']) : '';
        $data['core'] = $this->classConfig->getBoolValue($member, 'core');
        return $data;
    }
}
