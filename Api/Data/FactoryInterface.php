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
interface FactoryInterface
{
    /**
     * @var string
     */
    const ATTRIBUTE_FACTORY_KEY = 'attribute';

    /**
     * @var string
     */
    const ENTITY_FACTORY_KEY = 'entity';

    /**
     * @var string
     */
    const RELATION_FACTORY_KEY = 'relation';

    /**
     * @param array $data
     * @return ModelInterface
     */
    public function create(array $data = []);
}
