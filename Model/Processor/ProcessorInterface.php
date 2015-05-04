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
namespace Umc\Base\Model\Processor;

use Umc\Base\Model\Core\AbstractModel;

interface ProcessorInterface
{
    /**
     * process element
     *
     * @param $element
     * @param string $rawContent
     * @return mixed
     */
    public function process($element, $rawContent = '');

    /**
     * set model for processing
     *
     * @param AbstractModel $model
     * @return mixed
     */
    public function setModel(AbstractModel $model);

    /**
     * get model for processing
     *
     * @return mixed
     */
    public function getModel();
}
