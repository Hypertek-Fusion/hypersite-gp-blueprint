<?php

namespace ACPT\Includes;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    advanced-custom-post-type
 * @subpackage advanced-custom-post-type/includes
 * @author     Mauro Cassani <maurocassani1978@gmail.com>
 */
class ACPT_Activator
{
	/**
	 * Activate plugin
	 *
	 * @throws \Exception
	 * @since    1.0.0
	 */
    public static function activate()
    {
        if ( version_compare( PHP_VERSION, '7.3', '<=' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'This plugin requires PHP Version 7.3 or greater.  Sorry about that.', 'acpt' ) );
        }

        $old_version = get_option('acpt_version', 0);
        ACPT_DB::createSchema(ACPT_PLUGIN_VERSION, get_option('acpt_current_version') ?? oldACPTPluginVersion($old_version));
        ACPT_DB::sync();

        // fix `The plugin generated unexpected output` error
	    if ( ob_get_length() > 0 ) {
		    ob_get_clean();
	    }
    }
}