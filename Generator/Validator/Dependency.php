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
namespace Umc\Base\Generator\Validator;

use Umc\Base\Api\Data\ModelInterface;

class Dependency
{
    /**
     * @param ModelInterface $model
     * @param array $config
     * @return bool
     */
    public function validateDepend(ModelInterface $model, array $config)
    {
        if (!isset($config['depends'])) {
            return true;
        }
        foreach ($config['depends'] as $dependsGroup) {
            //groups are combined using OR
            $groupValid = true;
            //depend tag values are combined using AND
            if (!isset($dependsGroup['depend'])) {
                $dependsGroup['depend'] = [];
            }
            foreach ($dependsGroup['depend'] as $field => $fieldSettings) {
                $allowedValues = [];
                if (!isset($fieldSettings['val'])) {
                    $fieldSettings['val'] = [];
                }
                foreach ($fieldSettings['val'] as $val) {
                    $allowedValues[] = $val['value'];
                }
                //data value must be in allowed values
                if (!in_array($model->getDataUsingMethod($field), $allowedValues)) {
                    $groupValid = false;
                    break;
                }
            }
            //if at least one group is valid then everything is valid
            if ($groupValid) {
                return true;
            }
        }
        return false;
    }
}
