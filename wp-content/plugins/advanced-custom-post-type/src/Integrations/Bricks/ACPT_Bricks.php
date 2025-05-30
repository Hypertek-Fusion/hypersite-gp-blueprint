<?php

namespace ACPT\Integrations\Bricks;

use ACPT\Core\Models\Settings\SettingsModel;
use ACPT\Includes\ACPT_Plugin;
use ACPT\Integrations\AbstractIntegration;
use ACPT\Utils\Settings\Settings;

class ACPT_Bricks extends AbstractIntegration
{
	const MINIMUM_BRICKS_VERSION = '1.6.2';

    /**
     * @inheritDoc
     */
    protected function isActive()
    {
        $theme = wp_get_theme();

		if(( 'Bricks' == $theme->name or 'Bricks' == $theme->parent_theme )){
			$enabledMeta = Settings::get(SettingsModel::ENABLE_META, 1) == 1;

			return $enabledMeta == 1 and $this->checkThemeVersion($theme, self::MINIMUM_BRICKS_VERSION);
		}

        return false;
    }

    /**
     * @inheritDoc
     */
    protected function runIntegration()
    {
	    add_filter( 'bricks/dynamic_data/register_providers', function( $providers ) {

		    require_once ACPT_PLUGIN_DIR_PATH . '/src/Integrations/Bricks/providers/provider-acpt.php';

		    if ( class_exists( ACPT_Plugin::class ) ) {
			    $providers[] = 'acpt';
		    }

		    return $providers;
	    } );
    }
}
