<?php
/**
 * ModuleServiceProvider
 *
 * @copyright Copyright © 2020 Redstage. All rights reserved.
 * @author    cgartner@redstage.com
 */

namespace Cagartner\Picpay\Providers;

use Cagartner\Picpay\Models\PicpayTransaction;
use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        PicpayTransaction::class,
    ];
}