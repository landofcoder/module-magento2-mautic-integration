<?php
/**
 * Copyright © landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Mautic\Model\Config\Source;

class OauthVersion implements \Magento\Framework\Option\ArrayInterface
{
    const AUTH_OAUTH1 = 'OAuth1a';
    const AUTH_OAUTH2 = 'OAuth2';
    const AUTH_BASIC = 'BasicAuth';

    public function toOptionArray()
    {
        return [
            // ['value'=> self::AUTH_OAUTH1, 'label'=> __('OAuth 1')],
            ['value'=> self::AUTH_OAUTH2, 'label'=> __('OAuth 2')],
            ['value'=> self::AUTH_BASIC, 'label'=> __('Basic Auth')]
        ];
    }
}

