<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Mautic
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
declare(strict_types=1);

namespace Lof\Mautic\Helper;

class MappingField extends Data
{
   /**
     * Mapping mautic data to contacts data
     * @param array
     * @return array
     */
    public function mappingMauticData(array $data = [])
    {
        return $data;
    }

    /**
     * Mapping contact data to mautic data
     * @param array
     * @return array
     */
    public function mappingContactData(array $data = [])
    {
        return $data;
    }
}
