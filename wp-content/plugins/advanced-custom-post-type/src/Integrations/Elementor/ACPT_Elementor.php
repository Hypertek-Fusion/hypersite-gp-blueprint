<?php

namespace ACPT\Integrations\Elementor;

use ACPT\Core\Models\Belong\BelongModel;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Core\Models\Meta\MetaGroupModel;
use ACPT\Core\Models\Settings\SettingsModel;
use ACPT\Core\Repository\MetaRepository;
use ACPT\Integrations\AbstractIntegration;
use ACPT\Integrations\Elementor\Controls\CssControl;
use ACPT\Integrations\Elementor\Controls\DateFormatControl;
use ACPT\Integrations\Elementor\Controls\ElementsControl;
use ACPT\Integrations\Elementor\Controls\HeightControl;
use ACPT\Integrations\Elementor\Controls\RenderControl;
use ACPT\Integrations\Elementor\Controls\ShortcodeControl;
use ACPT\Integrations\Elementor\Controls\TargetControl;
use ACPT\Integrations\Elementor\Controls\WidthControl;
use ACPT\Integrations\Elementor\Controls\WrapperControl;
use ACPT\Integrations\Elementor\Widgets\WidgetGenerator;
use ACPT\Utils\Settings\Settings;
use Elementor\Widgets_Manager;

class ACPT_Elementor extends AbstractIntegration
{
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    /**
     * @inheritDoc
     */
    protected function isActive()
    {
	    $isActive = is_plugin_active( 'elementor/elementor.php' );

	    if(!$isActive){
		    return false;
	    }

	    $enabledMeta = Settings::get(SettingsModel::ENABLE_META, 1) == 1;

        return $enabledMeta and $isActive;
    }

    /**
     * @inheritDoc
     */
    protected function runIntegration()
    {
        if($this->checkIfElementorVersionIsCompatible()){
            add_action( 'elementor/controls/register', [$this, 'registerElementorControls'] );
            add_action( 'elementor/elements/categories_registered', [$this, 'addElementorWidgetCategory'] );
            add_action( 'elementor/widgets/register', [$this, 'registerElementorWidgets'] );
        }
    }

    /**
     * Register Elementor controls
     *
     * @param $controls_manager
     */
    public function registerElementorControls( $controls_manager )
    {
	    $controls_manager->register( new ShortcodeControl() );
    }

    /**
     * Add ACPT category to Elementor
     *
     * @param $elements_manager
     */
    public function addElementorWidgetCategory( $elements_manager )
    {
        $elements_manager->add_category(
            'acpt',
            [
                'title' => esc_html__( 'ACPT', ACPT_PLUGIN_NAME ),
                'icon' => 'fa fa-plug',
            ]
        );
    }

    /**
     * https://github.com/wpacademy/wpac-material-cards-elementor
     *
     * @param Widgets_Manager $widgetsManager
     * @throws \Exception
     * @since 1.0.3
     */
    public function registerElementorWidgets(Widgets_Manager $widgetsManager)
    {
    	try {
            $fieldGroups = MetaRepository::get([
                'clonedFields' => true
            ]);

            foreach ($fieldGroups as $fieldGroup){
                if(count($fieldGroup->getBelongs()) > 0){
                    foreach ($fieldGroup->getBelongs() as $belong){
                        $this->registerFields($fieldGroup, $widgetsManager, $belong);
                    }
                }
            }
	    } catch (\Exception $exception){}
    }

    /**
     * @param MetaGroupModel $metaGroup
     * @param Widgets_Manager $widgetsManager
     * @param BelongModel $belong
     * @throws \Exception
     */
    private function registerFields(MetaGroupModel $metaGroup, Widgets_Manager $widgetsManager, BelongModel $belong)
    {
        foreach ($metaGroup->getBoxes() as $metaBox){
            foreach ($metaBox->getFields() as $boxFieldModel){

                $notAllowedTypes = [
                    MetaFieldModel::CLONE_TYPE
                ];

                if(!in_array($boxFieldModel->getType(), $notAllowedTypes)){
                    $boxFieldModel->setBelongsAndFindLabels($belong);
                    $widgetsManager->register( new WidgetGenerator([], [
                        'boxFieldModel' => $boxFieldModel,
                        'find' => $boxFieldModel->getFindLabel(),
                    ]));
                }
            }
        }
    }

    /**
     * @return bool
     */
    private function checkIfElementorVersionIsCompatible()
    {
        $elementorPlugin = __DIR__.'/../../../../elementor/elementor.php';

        if( !file_exists($elementorPlugin) ){
            return false;
        }

        $pluginData = get_plugin_data( $elementorPlugin );

        return version_compare( $pluginData['Version'], self::MINIMUM_ELEMENTOR_VERSION, '>=' );
    }
}
