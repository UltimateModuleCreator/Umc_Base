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
namespace Umc\Base\Model;

use Magento\Framework\Escaper;

class Tooltip extends Umc
{
    /**
     * escaper reference
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * title of the tooltip
     *
     * @var string
     */
    protected $title;

    /**
     * message of the tooltip
     *
     * @var string
     */
    protected $message;

    /**
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Escaper $escaper,
        array $data = []
    ) {
        $this->escaper = $escaper;
        parent::__construct($data);
    }

    /**
     * set tooltip title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * get tooltip title
     *
     * @param bool $escaped
     * @return string
     */
    public function getTitle($escaped = true)
    {
        if ($escaped) {
            return $this->escaper->escapeHtml($this->title);
        }
        return $this->title;
    }

    /**
     * set the tooltip message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * get tooltip message
     *
     * @return string
     */
    public function getMessageHtml()
    {
        return $this->message;
    }
}
