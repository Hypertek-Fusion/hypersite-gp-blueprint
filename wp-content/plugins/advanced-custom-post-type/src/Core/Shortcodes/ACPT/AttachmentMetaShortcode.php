<?php

namespace ACPT\Core\Shortcodes\ACPT;

use ACPT\Constants\MetaTypes;

class AttachmentMetaShortcode extends AbstractACPTShortcode
{
	/**
	 * @param array $atts
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function render($atts)
	{
		if(!isset($atts['box']) or !isset($atts['field'])){
			return '';
		}

		if(!isset($atts['pid'])){
			return '';
		}

		$pid = $atts['pid'];
		$postType = 'attachment';

		return $this->renderShortcode($pid, MetaTypes::MEDIA, $postType, $atts);
	}
}
