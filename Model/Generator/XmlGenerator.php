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
namespace Umc\Base\Model\Generator;

class XmlGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * license text
     *
     * @var string
     */
    protected $license;

    /**
     * @param string $content
     * @return string
     */
    public function postProcess($content)
    {
        try {
            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($content);
            $processed =  preg_replace_callback('/^( +)</m', function($a) {
                return str_repeat(' ',intval(strlen($a[1]) / 2) * 4).'<';
            }, $dom->saveXML($dom->firstChild));
            return $this->getHeader().$this->getLicense().$processed.$this->getEol();
        }
        catch (\Exception $up) {
            throw $up;
        }
    }

    /**
     * get the file header
     *
     * @return string
     */
    public function getHeader()
    {
        return '<?xml version="1.0"?>'.$this->getEol();
    }

    /**
     * process license text
     *
     * @return string
     */
    protected function getLicense()
    {
        if (is_null($this->license)) {
            $license    = trim($this->module->getSettings()->getLicense());
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
            $eol = $this->getEol();
            $top = '<!--'.$eol;
            $footer = $eol.'-->'.$eol;
            $processed = $top.'/**'.$eol;
            foreach ($lines as $line) {
                $processed .= ' * '.$line.$eol;
            }
            $processed .= ' */'.$footer;
            $this->license = $this->license = $this->module->filterContent($processed);
        }
        return $this->license;
    }
}
