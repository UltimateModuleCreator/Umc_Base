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
namespace Umc\Base\Processor;

use Umc\Base\Api\Data\ModelInterface;

class PartProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * @param ModelInterface $mainModel
     * @param array $part
     * @param string $rawContent
     * @return string
     */
    public function process(ModelInterface $mainModel, $part, $rawContent = '')
    {
        $content = '';
        foreach ($this->getModelsToProcess($mainModel) as $model) {
            if ($this->dependencyValidator->validateDepend($model, $part)) {
                $content .= $model->filterContent($rawContent);
            }
        }
        return $content;
    }
}
