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
use Umc\Base\Generator\Validator\Dependency;
use Umc\Base\Provider\Processor\ProviderInterface;
use Umc\Base\Parser\Member as MemberParser;

class MemberProcessor extends AbstractProcessor implements ProcessorInterface
{
    /**
     * member parser
     *
     * @var \Umc\Base\Parser\Member
     */
    protected $memberParser;

    /**
     * @param Dependency $dependencyValidator
     * @param ProviderInterface $modelProvider
     * @param MemberParser $memberParser
     */
    public function __construct(
        Dependency $dependencyValidator,
        ProviderInterface $modelProvider,
        MemberParser $memberParser
    ) {
        $this->memberParser = $memberParser;
        parent::__construct($dependencyValidator, $modelProvider);
    }

    /**
     * @param ModelInterface $mainModel
     * @param array $member
     * @param string $rawContent
     * @return array|bool
     */
    public function process(ModelInterface $mainModel, $member, $rawContent = '')
    {
        $members = [];
        foreach ($this->getModelsToProcess($mainModel) as $model) {
            if ($this->dependencyValidator->validateDepend($model, $member)) {
                $parsedMember = $this->memberParser->parse($model, $member);
                $members[$parsedMember['id']] = $parsedMember;
            }
        }
        if (count($members)) {
            return $members;
        }
        return false;
    }
}
