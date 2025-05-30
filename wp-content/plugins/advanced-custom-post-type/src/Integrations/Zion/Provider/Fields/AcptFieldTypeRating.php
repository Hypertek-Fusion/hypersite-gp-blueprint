<?php

namespace ACPT\Integrations\Zion\Provider\Fields;

use ACPT\Core\Helper\Strings;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Integrations\Zion\Provider\Utils\FieldSettings;
use ACPT\Integrations\Zion\Provider\Utils\FieldValue;
use ACPT\Utils\Wordpress\Translator;

class AcptFieldTypeRating extends AcptFieldBase
{
	/**
	 * Retrieve the list of all supported field types
	 * @return array
	 */
	public static function getSupportedFieldTypes()
	{
		return [
			MetaFieldModel::RATING_TYPE,
		];
	}

	/**
	 * @return string
	 */
	public function get_category()
	{
		return self::CATEGORY_TEXT;
	}

	/**
	 * @return string
	 */
	public function get_id()
	{
		return 'acpt-field-rating';
	}

	/**
	 * @return string
	 */
	public function get_name()
	{
		return Translator::translate( 'ACPT Rating field');
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function get_options()
	{
		return array_merge(
			parent::get_options(),
			[
				'size' => [
					'type'        => 'number',
					'title'       => Translator::translate('Size (px)'),
					'description' => Translator::translate('Set the icon size.'),
					'default'     => '24',
				],
				'color' => [
					'type'        => 'colorpicker',
					'title'       => Translator::translate('Color'),
					'description' => Translator::translate('Select the icon color.'),
					'default'     => '#000000',
				],
			]
		);
	}

	/**
	 * @param mixed $fieldObject
	 *
	 * @throws \Exception
	 */
	public function render($fieldObject)
	{
		//#! Invalid entry, nothing to do here
		if(empty($fieldObject['field_name'])) {
			return;
		}

		$fieldSettings = FieldSettings::get($fieldObject['field_name']);

		if($fieldSettings === false or empty($fieldSettings)){
			return;
		}

		/** @var MetaFieldModel $metaFieldModel */
		$metaFieldModel = $fieldSettings['model'];
		$belongsTo = $fieldSettings['belongsTo'];

		if(!$this->isSupportedFieldType($metaFieldModel->getType())){
			return;
		}

		$rawValue = FieldValue::raw($belongsTo, $metaFieldModel);

		$size = $fieldObject['size'] ?? 24;
		$color = $fieldObject['color'] ?? '#000000';

		echo '<span style="'.$this->styleAttributes($size, $color).'">'.Strings::renderStars($rawValue, $size).'</span>';
	}

	/**
	 * @param string $size
	 * @param null $color
	 *
	 * @return string
	 */
	private function styleAttributes($size = '18', $color = null)
	{
		$attributes = 'font-size: '.$size.'px;';

		if($color){
			$attributes .= 'color: '.$color.'; fill: '.$color.';';
		}

		return $attributes;
	}
}