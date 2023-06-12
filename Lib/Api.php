<?php
/**
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 *
 * @see        http://mautic.org
 *
 * @license     MIT http://opensource.org/licenses/MIT
 */

namespace Lof\Mautic\Lib;

/**
 * Base API class.
 */
class Api extends \Mautic\Api\Api
{
    /**
     * Set the base URL for API endpoints.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setBaseUrl($url)
    {
        $url = $url ? $url : "";
        if ('/' != @substr($url, -1)) {
            $url .= '/';
        }

        if ('api/' != @substr($url, -4, 4)) {
            $url .= 'api/';
        }

        $this->baseUrl = $url;

        return $this;
    }
}
