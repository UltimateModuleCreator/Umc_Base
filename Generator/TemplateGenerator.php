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
namespace Umc\Base\Generator;

class TemplateGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * license text
     *
     * @var string
     */
    protected $license;

    /**
     * add license
     *
     * @param string $content
     * @return string
     */
    protected function postProcess($content)
    {
        return $this->getLicense().$content;
    }

    /**
     * get license text
     *
     * @return string
     */
    protected function getLicense()
    {
        if ($this->license === null) {
            $text = $this->module->getLicense();
            $eol = $this->getEol();
            $license = trim($text);
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
            $top = $this->getLicensePrefix();
            $processed = $top.'/**'.$eol;
            foreach ($lines as $line) {
                $processed .= ' * '.$line.$eol;
            }
            $processed .= ' */'.$eol.$this->getLicenseSuffix().$this->getEol();
            $this->license = $this->module->filterContent($processed);
        }
        return $this->license;
    }

    /**
     * @return string
     */
    protected function getLicensePrefix()
    {
        return '<?php '.$this->getEol();
    }

    /**
     * @return string
     */
    protected function getLicenseSuffix()
    {
        return '?>';
    }
}
